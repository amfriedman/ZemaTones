var esplayer_debug = false;
/*var esplayer_isAdmin = false;
if (typeof soundManager == 'undefined') {
	esplayer_isAdmin = true;
}*/

var Array_EsAudioPlayer = new Array();
var esp_playing_no = 0;
var esp_auto_playing = 0;
var esp_auto_playing_player_num = 0;
var soundManager_ready = false;
var esplayer_jquery_prepared = false;

jQuery(document).ready(function(){
	esplayer_jquery_prepared = true;

	for (var i=1; i<2048 ; i++) {
		var params = jQuery("#esplayervar"+i).val();
		if (typeof params == 'undefined') break;
		var args = params.split("|");
		var a = new EsAudioPlayer(
			args[0],
			args[1],
			args[2],
			args[3],
			args[4],
			args[5],
			args[6],
			args[7],
			args[8],
			args[9],
			args[10],
			(args[11].toLowerCase()=="true"),	//loop
			(args[12].toLowerCase()=="true"),	//autoplay
			args[13],
			args[14],
			args[15],
			args[16]);
	}
	esplayer_autoplay(i-1);
});

if (!esplayer_isAdmin) {
	soundManager.url = esAudioPlayer_plugin_URL + '/swf/';
	soundManager.flashVersion = 9; // optional: shiny features (default = 8)
	soundManager.useFlashBlock = false; // optionally, enable when you're ready to dive in
	// enable HTML5 audio support, if you're feeling adventurous. iPad/iPhone will always get this.
	soundManager.useHTML5Audio = true;
	soundManager.preferFlash = true;
	soundManager.debugMode = false;
	soundManager.debugFlash = false;
	soundManager.allowpolling = true;
	soundManager.useConsole = true;
	soundManager.onready(function() {
		soundManager_ready = true;
	});
}

function esplayer_autoplay(player_num)
{
	player_num = typeof(player_num) != 'undefined' ? player_num : 0;
	if (player_num > 0) esp_auto_playing_player_num = player_num;
	if (esp_auto_playing < 0) return;

	if (Array_EsAudioPlayer.length < esp_auto_playing_player_num) {
		setTimeout('esplayer_autoplay()', 100);
		return;
	}

	if (esp_playing_no) {
		setTimeout('esplayer_autoplay()', 100);
		return;
	}

	for (var i=esp_auto_playing; i<esp_auto_playing_player_num; i++) {
		if (Array_EsAudioPlayer[i].autoplay) {
			esp_auto_playing = i;
			break;
		}
	}
	if (i>=esp_auto_playing_player_num) {
		esp_auto_playing = -1;
		return;
	}
	var ep = Array_EsAudioPlayer[esp_auto_playing];
	if ((ep.created && ep.mode!="slideshow") || (ep.created && ep.mode=="slideshow" && ep.tt_obj.ready && !ep.tt_obj.nowtotalplaying)) {
		if (ep.mode=="slideshow") {
			ep.tt_obj.loadimage();
			ep.tt_obj.launch_tt(0);
		} else {
			ep.func_acc_play_stop();
		}
	} else {
		setTimeout('esplayer_autoplay()', 100);
		return;
	}
}

function esplayer_autoplay_next()
{
	if (esp_auto_playing<0) return;
	esp_auto_playing ++;
	setTimeout('esplayer_autoplay()', 100);
}

var EsAudioPlayer = function(mode, id, sURL, width, height, v_pos, shadow_size, shadow_color, corner_size, smartphone_size, border_img, loop, autoplay, duration, img_id, artist, title) {
	this.basecolor_play = '';
	this.symbolcolor_play = '';
	this.basecolor_stop = '';
	this.symbolcolor_stop = '';
	this.basecolor_pause = '';
	this.symbolcolor_pause = '';
	this.color_slider_line = '';
	this.color_slider_knob = '';

	this.n=0;
	this.sURLs = new Array();
	this.sURLs[0] = sURL;
	this.mySound = new Array();
	this.mySoundPosition = new Array();
	this.play=false;
	this.pause=false;
	this.created=false;
	this.preventDrawing = false;
	this.flgInitializing_beforePlaying=false;
	this.canvas = 0;
	this.that = this;
	this.id = id;
	this.img_id = img_id;
	this.width_org = width;
	this.height_org = height;
	this.width = width;
	this.height = height;
	this.v_pos_org = v_pos;
	this.v_pos = v_pos;
	this.canvas_width = width;
	this.canvas_height = height;
	this.shw_rate = shadow_size;
	this.shw_color = shadow_color;
	this.corner_rate = (corner_size==-999)?-999:corner_size/100.0;
	this.smartphone_rate = (smartphone_size==-999)?-999:smartphone_size/100.0;
	this.shw_size = 0;
	this.start_anim_retry = 0;
	this.start_time = 0;
	this.anim_ok = false;
	this.nowPlaying = 0;
	this.border_img = border_img;
	this.debug_msg = '';
	this.duration = this.calc_duration(duration); //manually specified duration of audio (milliseconds)
	this.mode = mode;	// 'simple' or 'slideshow' or 'imgclick'
	this.loop = loop;
	this.autoplay = autoplay;	
	this.tt_id_list = new Array();   // Time table ID list
	this.ready = false;
	
	var that = this;

	var callMethod_init = function() {that.init();};
	this.init_id = setInterval(callMethod_init, 100);
};


// function name: init
// description : initialization
// argument : void
EsAudioPlayer.prototype.init = function() 
{
	if (!esplayer_jquery_prepared) return;
	clearInterval(this.init_id);
	var that = this;
	this.GetSizeInPx();

	this.sx_st = this.height + (this.height*0.15);
	this.sx_en = this.width - (this.height*0.3);
	this.sy= this.canvas_height /2;
	this.slider_x = -1;
	this.slider_y = -1;
	this.slider_mouse_ofs_x = 0;
	this.slider_mouse_ofs_y = 0;

	this.slider_length = (this.width>=this.height*2) ? this.sx_en - this.sx_st : 0;
	this.slider_drag=false;
	this.slider_width = this.height*0.1;
	this.slider_height = this.height*0.5;
	this.slider_img = new Image();

	if (this.mode=="simple") {
		this.isIPhone = (new RegExp( "(iPhone|iPod|iPad)", "i" )).test(navigator.userAgent);
		this.isAndroid = (new RegExp( "(Android)", "i" )).test(navigator.userAgent);
		//this.isSmartphone = (this.isIPhone || this.isAndroid);
		this.isSmartphone = ('ontouchstart' in window);
		this.isGecko = navigator.userAgent.match(/SeaMonkey|Firefox/i) && navigator.userAgent.match(/rv:[56].0/i);
		this.isIE = (new RegExp( "MSIE (3|4|5|6|7|8)", "i" )).test(navigator.userAgent);
		jQuery('#'+this.id).bind(this.isSmartphone ? 'touchstart':'mousedown' ,function(event){
			that.onClick(event);
		});
		//jQuery('#'+this.id).attr("tabindex","0");
		//jQuery('#'+this.id).keypress(function(event){
		//	alert(event.which);
		//});
	}

	this.getSetting();

	// prepare animation
	if (!this.isIE) {
		if (this.mode=="simple") {
			var callMethod_initCanvas = function() {that.initCanvas();};
			this.initCanvas_id = setInterval(callMethod_initCanvas, 20);
		} else {
			this.anim_ok = true;
		}
	} else {
		var callMethod_IE = function() {that.startAnim_IE();};
		this.int_IE_id = setInterval(callMethod_IE, 20);
	}

	// initialize slideshow
	if (this.mode=="slideshow") {
		this.tt_id_list = this.sURLs[0].split(',');
		this.tt_obj = new EsAudioPlayer_tt(this);
	} else {
	}

	if (this.mode=="imgclick") {
		var el = jQuery('#'+this.img_id);
		if (jQuery('#'+this.img_id).parent().get(0).tagName.toUpperCase() == "A") {
			jQuery('#'+this.img_id).parent().contents().unwrap();
		}

		jQuery(el).bind(that.isSmartphone ? "touchstart":"mousedown", function(event){
			esp_auto_playing = -1;
			that.func_play_stop(); 
		});
		jQuery(el).css('cursor','pointer');
	}

	// sound initialization
	var  callMethod_init = function() {that.initSound();};
	setInterval(callMethod_init, 200);

	// start animation
	var callMethod = function() {that.anim();};
	setInterval(callMethod, 500);

	// add this object to the player list (for exclusive play control)
	Array_EsAudioPlayer[Array_EsAudioPlayer.length] = this;
};


// function name: GetSizeInPx
// description : convert units of parameters from non-px to px
// argument : void
EsAudioPlayer.prototype.GetSizeInPx = function() 
{
	var elmt0 = document.createElement('div');
	elmt0.setAttribute('id','tmpdiv'+this.id);
	document.getElementsByTagName('body')[0].appendChild(elmt0);
	var elmt = jQuery('#tmpdiv'+this.id);

	var elm = jQuery('#'+this.id+'_tmpspan');

	//var org_fontsize = jQuery(elm).css('width');
	jQuery(elmt).css('width', this.width_org);

	this.width = parseInt(jQuery(elmt).css('width').replace('px',''));
	jQuery(elmt).css('width', this.height_org);
	this.height = parseInt(jQuery(elmt).css('width').replace('px',''));
	var vpos = this.v_pos_org;
	var vpos_sign = vpos.substr(0,1);
	if (vpos_sign != '-') vpos_sign="";
	if (vpos_sign == '-') vpos = vpos.substr(1);

	jQuery(elmt).css('width', vpos);
	this.v_pos = parseInt(jQuery(elmt).css('width').replace('px',''));
	if (vpos_sign == '-') this.v_pos = -this.v_pos;
	jQuery(elm).css('top', this.v_pos+'px');
};


// function name: calc_duration
// description : convert duration given manually from "MM:SS.ss" to millisecond.
// argument : void
EsAudioPlayer.prototype.calc_duration = function(duration) 
{
	var dur = 0;
	var sec = "";
	var flt=0;
	if (duration=="") return 0;
	split1 = duration.split(":");
	if (split1.length == 2) {
		dur = parseInt(split1[0]) * 60 * 1000;
		sec = split1[1];
	} else {
		sec = split1[0];
	}
	dur += parseFloat(sec) * 1000;
	return dur;
};


// function name: startAnim_IE
// description : start animation, after excanvas is ready (IE) 
// argument : void
EsAudioPlayer.prototype.startAnim_IE = function() 
{
	this.start_anim_retry++;
	if (this.start_anim_retry > 50) {
		var elem = document.getElementById(this.id);
		var av = !!(elem.getContext && elem.getContext('2d')); 
		if (av) {
			this.initCanvas();
			clearInterval(this.int_IE_id);
			this.anim_ok = true;
			return;
		}
	}
};

// function name: initCanvas
// description : initialize canvas element
// argument : void
EsAudioPlayer.prototype.initCanvas = function() 
{
	this.canvas = document.getElementById(this.id);

	// calculate canvas size 
	this.canvas_width = Math.ceil(this.width + this.shw_size);
	this.canvas_height= Math.ceil(this.height + this.shw_size);
//alert(this.width + ' ' + this.canvas_width + ' ' + this.shw_rate);
	if (this.slider_length) {
		//this.canvas_width += this.slider_length * 1.2;
		//this.sx_st = this.width+5+this.slider_width/2;
		//this.sx_en = this.canvas_width - 5-this.slider_width/2;
	}
	if (!(this.canvas!=null) && esplayer_debug) alert('Canvas could not be prepared. '+this.id+' is null');

	if (this.width_org.search(/px/i)==-1 || this.height_org.search(/px/i)==-1) {
		this.canvas.setAttribute("width",this.canvas_width);
		this.canvas.setAttribute("height",this.canvas_height);
		if (this.isIE) { /* in case of IE these procedures must be done twice.I don't know why. */
			this.canvas.setAttribute("width",this.canvas_width);
			this.canvas.setAttribute("height",this.canvas_height);
		}
	}
	this.anim_ok = true;
	clearInterval(this.initCanvas_id);
};




// function name: getSetting
// description : set default setting to the player object.
// argument : void
EsAudioPlayer.prototype.getSetting = function(force_reset) 
{
	fr = typeof(force_reset) != 'undefined' ? true : false;
	if (this.basecolor_play == '' || fr) this.basecolor_play = esplayer_basecolor_play;
	if (this.symbolcolor_play == '' || fr) this.symbolcolor_play = esplayer_symbolcolor_play;
	if (this.basecolor_stop == '' || fr) this.basecolor_stop = esplayer_basecolor_stop;
	if (this.symbolcolor_stop == '' || fr) this.symbolcolor_stop = esplayer_symbolcolor_stop;
	if (this.basecolor_pause == '' || fr) this.basecolor_pause = esplayer_basecolor_pause;
	if (this.symbolcolor_pause == '' || fr) this.symbolcolor_pause = esplayer_symbolcolor_pause;
	if (this.color_slider_line == '' || fr) this.color_slider_line = esplayer_color_slider_line;
	if (this.color_slider_knob == '' || fr) this.color_slider_knob = esplayer_color_slider_knob;
	if (this.shw_rate == -999 || fr) this.shw_rate = esplayer_shadowsize;
	if (this.shw_color == '' || fr) this.shw_color = esplayer_shadowcolor;
	if (this.corner_rate == -999 || fr) this.corner_rate = esplayer_cornersize/100.0;
	if (this.smartphone_rate == -999 || fr) this.smartphone_rate = esplayer_smartphonesize/100.0;
	this.shw_size = Math.min(   Math.min(this.width, this.height)*this.shw_rate   , 100);
	if (fr) {this.preventDrawing=false;this.anim();}
};


// function name: initSound
// description : initialize Soundmanager2 object
// argument : void
EsAudioPlayer.prototype.initSound = function() 
{
	if (esplayer_isAdmin) {
		// Creating dummy soundmanager object for preview in the admin page.
		function DMY(){}; 
		DMY.prototype.setPosition = function(p){this.position=p;};
		DMY.prototype.play = function(){this.playState=1;};
		DMY.prototype.stop = function(){this.playState=0;};
		DMY.prototype.pause = function(){};
		this.mySound[0]=new DMY;
		this.mySound[0].BytesLoaded = 1; 
		this.mySound[0].playState=0;
		this.mySound[0].duration=this.duration;
		this.mySound[0].durationEstimate=this.duration;
		this.mySound[0].position=0;
		this.mySound[0].bytesTotal=1;
		this.mySoundPosition[0] = 0;
		this.created=true;
		return;
	}

	if (!this.created) {

		if (this.mode == "slideshow" && !esp_tt_data_ready) {
			return;
		}

		if (soundManager_ready) {
		
			if (this.mode == "slideshow") {	
				this.nowPlaying = 0;
				var i;
				for (i=0; i<this.tt_id_list.length; i++) {
					this.sURLs[i] = esp_tt_data[this.tt_id_list[i]].url;
					if (soundManager.canPlayURL(this.sURLs[i])) {
						this.mySound[i] = soundManager.createSound({
							id:this.tt_id_list[i],
							url:this.sURLs[i],
							autoLoad:false,
							stream: true,
							autoPlay: false,
							volume:100
						});
					}
					this.mySoundPosition[i] = 0;
				}
				this.created=true;
			} else {
				if (soundManager.canPlayURL(this.sURLs[0])) {
					this.mySound[0] = soundManager.createSound({
						id:this.id,
						url:this.sURLs[0],
						autoLoad:false,
						stream: true,
						autoPlay: false,
						volume:100
					});
				}
				this.mySoundPosition[0] = 0;
				this.created=true;

			}
		}
	}
};



// function name: onClick
// description : process click/touch event
// argument : ev (event)
EsAudioPlayer.prototype.onClick = function(ev)
{
	if (esplayer_isAdmin) return;

	this.preventDrawing = false;
	this.ofs = jQuery(this.canvas).offset();

	var px = this.getEv(ev).pageX - this.ofs.left;
	var py = this.getEv(ev).pageY - this.ofs.top;

	var btn_width = this.width>=this.height*2 ? this.height : this.width;
	if (px>=1 && py>=1 && px<btn_width/*-this.shw_size*/ && py<this.height/*-this.shw_size*/) {
		esp_auto_playing = -1;
		this.func_play_stop();
	}

	if (this.slider_x > 0) {
		var w = this.slider_width;
		var h = this.slider_height;
		if (px > this.slider_x - w*4 && px < this.slider_x+w*3.5 && py>this.slider_y-h/2-w*5 && py<this.slider_y+h/2+w*5) {
			this.slider_mouse_ofs_x = px - this.slider_x;
			this.slider_mouse_ofs_y = py - this.slider_y;
			this.slider_drag = true;
			var that = this;
			jQuery(document).bind(this.isSmartphone?"touchmove":"mousemove", function(ev){that.onMouseMove(ev);});
			jQuery(document).bind(this.isSmartphone?"touchend":"mouseup", function(ev){that.onMouseUp(ev);});
			return;
		}
	}

	if (	px >= this.calc_sx(0)-this.slider_width/2 &&
		py >= this.sy -this.slider_height/2 &&
		px <= this.calc_sx(-1)+this.slider_width/2 &&
		py <= this.sy + this.slider_height/2 ) {
		this.slider_mouse_ofs_x=0;
		this.mySound.setPosition(this.calc_pos(px));
	}

};


EsAudioPlayer.prototype.getEv = function( event )
{
	return( this.isSmartphone ? window.event.changedTouches[ 0 ] :  event );
	//return(  event );
};


// function name: onMouseMove
// description : process mouse move event (moving slider)
// argument : ev (event)
EsAudioPlayer.prototype.onMouseMove = function (ev)
{
	this.slider_x = this.getEv(ev).pageX - this.slider_mouse_ofs_x - this.ofs.left;
	this.slider_x = Math.max(this.calc_sx(0), this.slider_x);
	this.slider_x = Math.min(this.calc_sx(this.mySound[this.nowPlaying].duration),this.slider_x);
	this.preventDrawing = false;
	this.anim();
};

// function name: onMouseUp
// description : process mouse/touchpad release event
// argument : ev (event)
EsAudioPlayer.prototype.onMouseUp = function (ev)
{
	this.mySound[this.nowPlaying].setPosition(this.calc_pos(this.slider_x));
	var that = this;
	jQuery(document).unbind("mousemove touchmove mouseup touchend");
	this.slider_drag = false;
};
	

// function name: calc_sx
// description : calculate x-position of the slider from playing position of the sound 
// argument : pos: playing position(milliseconds) of the sound 
EsAudioPlayer.prototype.calc_sx = function(pos)
{
	if (pos<0) return this.sx_en;
	if (pos==0) return this.sx_st;
	var ms = this.mySound[this.nowPlaying];
	var est_dur = (this.duration>0) ? this.duration : ms.durationEstimate;
	var duration = (ms.bytesLoaded!=ms.bytesTotal) ? est_dur : ms.duration;
	return this.sx_st + (this.sx_en - this.sx_st)*pos / (duration);
};


// function name: calc_pos
// description : calculate playing position of the sound(milliseconds) from x-position of the slider
// argument : sx: absolute x-position of the slider
EsAudioPlayer.prototype.calc_pos = function(sx) 
{
	var ms = this.mySound[this.nowPlaying];
	var est_dur = (this.duration>0) ? this.duration : ms.durationEstimate;
	var duration = (ms.bytesLoaded!=ms.bytesTotal) ? est_dur : ms.duration;
	return (sx - this.sx_st) / (this.sx_en - this.sx_st) * duration;
};


// function name: draw_button_base
// description : draw background of the button
// argument : (x1,y1):position of top-left  (x2,y2):position of bottom-right base_color: color of background
EsAudioPlayer.prototype.draw_button_base = function(x1,y1,x2,y2, base_color)
{
	var ctx = this.canvas.getContext('2d');
	this.ie_shadow(ctx,x1,y1,x2,y2);
	ctx.fillStyle = base_color;
	this.set_button_shadow(ctx, true);
	ctx.fillRoundedRect/*fillRect*/(x1,y1,x2-x1,y2-y1, Math.min(x2-x1,y2-y1)*this.corner_rate);


	this.set_button_shadow(ctx, false);
};


// function name: draw_play_button
// description : draw play button
// argument : (x1,y1):position of top-left  (x2,y2):position of bottom-right  symbol_color: color of symbol
EsAudioPlayer.prototype.draw_play_button = function(x1,y1,x2,y2, symbol_color)
{
	var ctx = this.canvas.getContext('2d');
	ctx.beginPath();
	ctx.moveTo( x1+(x2-x1)*0.3 , y1+(y2-y1)*0.25 );
	ctx.lineTo( x1+(x2-x1)*0.8 , y1+(y2-y1)*0.5 );
	ctx.lineTo( x1+(x2-x1)*0.3 , y1+(y2-y1)*0.75 );
	ctx.closePath();
	ctx.fillStyle = symbol_color;
	ctx.fill();
};

// function name: draw_stop_button
// description : draw stop button
// argument : (x1,y1):position of top-left  (x2,y2):position of bottom-right  symbol_color: color of symbol
EsAudioPlayer.prototype.draw_stop_button = function(x1,y1,x2,y2, symbol_color)
{
	var ctx = this.canvas.getContext('2d');
	ctx.fillStyle = symbol_color;
	ctx.fillRect(x1+(x2-x1)*0.3 , y1+(y2-y1)*0.3, (x2-x1)*0.4, (y2-y1)*0.4);
};

// function name: draw_pause_button
// description : draw pause button
// argument : (x1,y1):position of top-left  (x2,y2):position of bottom-right  symbol_color: color of symbol
EsAudioPlayer.prototype.draw_pause_button = function(x1,y1,x2,y2, symbol_color)
{
	var ctx = this.canvas.getContext('2d');
	ctx.fillStyle = symbol_color;
	ctx.fillRect(x1+(x2-x1)*0.25 , y1+(y2-y1)*0.3, (x2-x1)*0.2, (y2-y1)*0.4);
	ctx.fillRect(x1+(x2-x1)*0.55 , y1+(y2-y1)*0.3, (x2-x1)*0.2, (y2-y1)*0.4);
};

// function name: set_button_shadow
// description : set parameters of shadow to the 2d context
// argument : ctx: target 2d context   sw: true(shadow on) false(shadow off)
EsAudioPlayer.prototype.set_button_shadow = function(ctx, sw)
{
	if (sw) {
		ctx.shadowBlur = this.shw_size*(this.isGecko?0.5:0.7);
		ctx.shadowOffsetX = this.shw_size/1.7;
		ctx.shadowOffsetY = this.shw_size/1.7*(this.isAndroid ? -1:1);

		ctx.shadowColor = this.shw_color;
	} else {
		ctx.shadowBlur = 0;
		ctx.shadowOffsetX=0;
		ctx.shadowOffsetY=0;
		ctx.shadowColor = '#000000';

	}
};

// function name: ie_shadow
// description : draw simple shadow (IE)
// argument : void
EsAudioPlayer.prototype.ie_shadow = function(ctx,x1,y1,x2,y2,color,size)
{
	if (this.isIE) {
		ctx.fillStyle = this.shw_color;
		var size = this.shw_size;
		ctx.fillRoundedRect/*fillRect*/(x1+size*.9,y1+size*.9,(x2-x1),(y2-y1), Math.min(x2-x1,y2-y1)*this.corner_rate);
	}
};


// function name: anim
// description : displaying player
// argument : void
EsAudioPlayer.prototype.anim = function() 
{
	if (this.mySound[this.nowPlaying] === undefined) return;

	// Turning off playing indicator by finishing playing.
	if (this.mySound[this.nowPlaying].playState) {
		this.flgInitializing_beforePlaying = false;
	}
	if (this.play && !this.mySound[this.nowPlaying].playState && !this.flgInitializing_beforePlaying && !esplayer_isAdmin) {
		this.func_stop();
		if (this.mode!="slideshow") esplayer_autoplay_next(); // even when "slideshow" mode, "anim" watches playing states.
	}

//jQuery('.main_meta h2').html(this.anim_ok?'true':'false');
	if (!this.anim_ok) return;
	if (this.preventDrawing) return;

//jQuery('.main_meta h2').html(cnt++);

	if (this.mode == "simple") {
		var ctx = this.canvas.getContext('2d');

		ctx.clearRect(0,0, this.canvas_width, this.canvas_height);

		var btn_width = this.width>=this.height*2 ? this.height : this.width;
		if (this.play) {
			if (this.pause) {
				this.draw_button_base(0,0,this.width-0,this.height-0,this.basecolor_play);
				this.draw_play_button(0,0,btn_width-0,this.height-0,this.symbolcolor_play);
			} else if (this.slider_length>0) {
				this.draw_button_base(0,0,this.width-0,this.height-0,this.basecolor_pause);
				this.draw_pause_button(0,0,btn_width-0,this.height-0,this.symbolcolor_pause);
			} else {
				this.draw_button_base(0,0,this.width-0,this.height-0,this.basecolor_stop);
				this.draw_stop_button(0,0,btn_width-0,this.height-0,this.symbolcolor_stop);
			}
		} else {
			this.draw_button_base(0,0,this.width-0,this.height-0,this.basecolor_play);
			this.draw_play_button(0,0,btn_width-0,this.height-0,this.symbolcolor_play);
		}
		//ctx.strokeStyle="rgba(0,0,255,1)";ctx.strokeRect(0,0, this.canvas_width, this.canvas_height);
		if (this.slider_length > 0) {
			var b_y = this.height * 0.5;
			ctx.fillStyle = this.color_slider_line;
			ctx.fillRect(this.sx_st, b_y-this.height*0.03/2, this.sx_en -this.sx_st ,this.height*0.03);
			var ms = this.mySound[this.nowPlaying];
			if (!(ms.bytesLoaded === undefined || ms.bytesLoaded === null)) {
				ctx.fillStyle = this.color_slider_line;
				ctx.fillRect(this.sx_st, b_y-this.height*0.06/2, (this.sx_en-this.sx_st)*(ms.bytesLoaded/ms.bytesTotal) ,this.height*0.06);
			}

			var position;
			if (ms.position) 
				position = ms.position;
			else 
				position = this.mySoundPosition[this.nowPlaying];

			var xpos = 0;
			if (!this.slider_drag) {
				this.slider_y = b_y;

				var est_dur = (this.duration>0) ? this.duration : ms.durationEstimate;
				var duration = (ms.bytesLoaded!=ms.bytesTotal) ? est_dur : ms.duration;

//jQuery('h2').html('ms.bytesLoaded='+ms.bytesLoaded+' ms.bytesTotal='+ms.bytesTotal+' ms.duration='+ms.duration+' ms.position='+ms.position);
				if (duration) {
					this.slider_x = this.sx_st+(this.sx_en-this.sx_st)*(position/duration);
					xpos = this.slider_x -this.slider_width/2;
				} else {
					xpos=this.sx_st-this.slider_width/2;
				}
			} else {
				xpos = this.slider_x - this.slider_width/2;
			}
			ctx.fillStyle = this.color_slider_knob;
			ctx.fillRect(xpos-this.slider_width/2 ,b_y-this.slider_height/2, this.slider_width, this.slider_height);
		}
	}

	if (!this.created) return;
	if (!this.play) this.preventDrawing = true;
};

CanvasRenderingContext2D.prototype.fillRoundedRect = fillRoundedRect;
    /*
        x: Upper left corner's X coordinate
        y: Upper left corner's Y coordinate
        w: Rectangle's width
        h: Rectangle's height
        r: Corner radius
    */
    function fillRoundedRect(x, y, w, h, r){
        this.beginPath();
        this.moveTo(x+r, y);
        this.lineTo(x+w-r, y);
        this.quadraticCurveTo(x+w, y, x+w, y+r);
        this.lineTo(x+w, y+h-r);
        this.quadraticCurveTo(x+w, y+h, x+w-r, y+h);
        this.lineTo(x+r, y+h);
        this.quadraticCurveTo(x, y+h, x, y+h-r);
        this.lineTo(x, y+r);
        this.quadraticCurveTo(x, y, x+r, y);
        this.fill();        
    }


// function name: func_play_stop
// description : toggle play/stop
// argument : accessibility option
//	"play/stop":
//	"play/pause":
//	"play":
EsAudioPlayer.prototype.func_play_stop = function()
{
	var acc="";
	if (arguments.length) acc=arguments[0];

	if (!this.created) return;

	if (!this.play) {
		this.func_stop_all_the_other_players();
	}

	if (this.pause) {
		this.pause = false;
		this.mySound[this.nowPlaying].resume();
		return;
	}

	if (!this.play) {
		this.start_time = (new Date()).getTime();
		this.mySound[this.nowPlaying].play();
		//this.mySound[this.nowPlaying].setPosition(0);
		this.play=true;
		esp_playing_no ++;
		this.flgInitializing_beforePlaying = true;
	} else {
		if ((this.slider_length > 0 && acc!="play/stop") || acc=="play/pause") {	//pause
			this.mySound[this.nowPlaying].pause();
			this.pause=true;
		} else if (acc != "play") {
			this.func_stop();
		}
	}
	this.anim();
};

// function name: func_stop_all_the_other_players
// description : stop all the other players
// argument : void
EsAudioPlayer.prototype.func_stop_all_the_other_players = function()
{
	if (esplayer_isAdmin) return;
	for (i=0; i<Array_EsAudioPlayer.length; i++) {
		if (Array_EsAudioPlayer[i].id != this.id) {
			if (Array_EsAudioPlayer[i].play) {
				Array_EsAudioPlayer[i].func_stop();
				if (!!Array_EsAudioPlayer[i].tt_obj) {
					Array_EsAudioPlayer[i].tt_obj.stop_slideshow();
				}
			}
		}
	}
};

// function name: func_acc_play
// description : play (accessible button)
// argument : void
EsAudioPlayer.prototype.func_acc_play = function()
{
	if (!this.play) {
		this.preventDrawing = false;
		this.func_play_stop();
	}
};

// function name: func_acc_stop
// description : stop (accessible button)
// argument : void
EsAudioPlayer.prototype.func_acc_stop = function()
{
	this.func_stop_all_the_other_players();
	this.func_stop();
};

// function name: func_acc_play_stop
// description : toggle play/stop (accessible button)
// argument : void
EsAudioPlayer.prototype.func_acc_play_stop = function()
{
	this.preventDrawing = false;
	this.func_play_stop("play/stop");
};

// function name: func_acc_play_pause
// description : toggle play/pause (accessible button)
// argument : void
EsAudioPlayer.prototype.func_acc_play_pause = function()
{
	this.preventDrawing = false;
	this.func_play_stop("play/pause");
};

// function name: func_acc_seek
// description : seek (accessible button)
// argument : amount: seek amount
//            unit : unit of amount ('sec':second 'pct':percent)
EsAudioPlayer.prototype.func_acc_seek = function(amount, unit)
{
	var ms = this.mySound[this.nowPlaying];
	var est_dur = (this.duration>0) ? this.duration : ms.durationEstimate;
	var duration = (ms.bytesLoaded!=ms.bytesTotal) ? est_dur : ms.duration;
	var cur = ms.position;
	var newpos = 0;

	if (unit=="pct") {
		newpos = cur + amount*0.01 * duration;
	} else if (unit == "sec") {
		newpos = cur + amount * 1000;
	} 
	if (newpos < 0) newpos=0;
	if (newpos > duration) return;
	ms.setPosition(newpos);
	this.preventDrawing = false;
	this.anim();
};


// function name: func_stop
// description : stop
// argument : void
EsAudioPlayer.prototype.func_stop = function()
{
	this.mySound[this.nowPlaying].stop();
	this.mySound[this.nowPlaying].setPosition(0);
	//soundManager.stopAll();
	this.play = false;
	this.pause = false;

	if (0) {  // avoid sm2's bug
		var url = this.mySound[this.nowPlaying].url;
		var id = this.mySound[this.nowPlaying].sID;
		this.mySound[this.nowPlaying].destruct();
		this.mySound[this.nowPlaying] = soundManager.createSound({
			id:id,
			url:url,
			autoLoad:false,
			stream: true,
			autoPlay: false,
			volume:100
		});
	}
	
	this.anim();
	esp_playing_no --;
};



EsAudioPlayer.prototype.switch_music_by_ttid = function(ttid)
{
	var i;
	for (i=0; i<this.tt_id_list.length; i++) {
		if (ttid == this.tt_id_list[i]) {
			this.nowPlaying = i;
			return;
		}
	
	}
};

EsAudioPlayer.prototype.getCurrentPosition = function()
{
	var clk_time = (new Date()).getTime();
	var cur_time = clk_time - this.start_time;
	var snd_time = this.mySound[this.nowPlaying].position;
	if (cur_time < snd_time) {
		this.start_time = clk_time - snd_time;
	}
	if (cur_time > snd_time && cur_time-snd_time>500 && snd_time < this.mySound[this.nowPlaying].duration) {
		this.start_time = clk_time - snd_time;
	}
	return clk_time - this.start_time;
};
