var esp_dbg="";

var esp_tt_data;  // time table data (gathered from all players)
var esp_tt_data_ready = false;
var esp_img_loadflg = new Array();
var cnt=0;

// function name: esp_count_nowloading()
// description : count the number of images currently being loaded and return it.
// argument : void
function esp_count_nowloading()
{
	var ret = 0;
	var id;

	for (id in esp_img_loadflg) {
		if (esp_img_loadflg[id][0]==true && esp_img_loadflg[id][1]==false) {
			ret ++;
		}
	}
	return ret;
}

jQuery(window).ready(function() {
	var i;
	var dat0 = jQuery.parseJSON(jQuery.base64_decode(esp_tt_data_encoded));
	esp_tt_data = new Array();
	for (i=0; i<dat0.length; i++) {
		esp_tt_data[dat0[i].id] = dat0[i];
	}
	esp_tt_data_ready = true;
});


var EsAudioPlayer_tt = function(esp_obj) {
	this.esp_obj = esp_obj;
	this.imgs = new Array();
	this.player_id = this.esp_obj.id;
	this.nowplaying = false;
	this.nowtotalplaying = false;
	this.tt_id_list = this.esp_obj.tt_id_list;
	this.playing_tt = this.tt_id_list[0];  // the id code of the time table now playing
	this.playing_tt_idx = 0;
	this.playing_tt_data_pos = 0; // the current reference position of 'time' data in the timetable
	this.playing_tt_data_num = 0; // number of data of slideshow now playing
	this.playing_tt_flag = false; // a boolean flag indicates if the slideshow is playing now
	this.loading_num = 0;
	this.box_id = '#'+this.player_id+'_tmpspan';
	this.box_width = jQuery(this.box_id).width();
	this.box_height = jQuery(this.box_id).height();
	this.ready = false;

	this.start_time = 0;	// the value of system clock at the time when slideshow started
	this.playing_func_id = -1;  // the administrative code of function which play the slideshow
	this.zindex = 10;  // the value of zindex of the image now showing

	jQuery(this.box_id).css('position','relative');

	// make div elements enclose images.
	var i;
	for (i=0; i<this.esp_obj.tt_id_list.length; i++) {
		this.makediv_for_img(this.esp_obj.tt_id_list[i]);
	}

	// load first images in each time tables.
	for (i=0; i<this.tt_id_list.length; i++) {
		this.load_an_image(this.tt_id_list[i], 0);		
	}

	// start loading
	var that = this;
	var callMethod_loadimage = function() {that.loadimage();};

	setInterval(callMethod_loadimage, 200);

	jQuery(this.box_id).css('cursor','pointer');

	jQuery(this.box_id).bind(this.esp_obj.isSmartphone ? 'touchstart' : 'mousedown', function(event){
		esp_auto_playing = -1;
		if (that.esp_obj.play === false) {
			this.that = this;
			that.launch_tt(0);
		} else {
			that.esp_obj.func_stop();
			that.stop_slideshow();
		}
	});
	this.ready = true;
}


// function name: launch_tt
// description : starting slideshow
// argument : tt_id (id of timetable)
EsAudioPlayer_tt.prototype.launch_tt = function(tt_id_list_idx)
{
	if (this.esp_obj.created === false) {
		return;
	}

	tt_id = this.tt_id_list[tt_id_list_idx];
	this.playing_tt = tt_id;
	this.prepare_tt(tt_id);
	this.nowplaying = true;
	this.nowtotalplaying = true;	

	this.playing_tt_data_pos = -1;
	var that = this;
	if (this.esp_obj.play) {
		this.esp_obj.func_stop();
	}
	this.esp_obj.switch_music_by_ttid (tt_id);

	this.playing_tt_data_num = esp_tt_data[tt_id].time.length;

	this.esp_obj.func_play_stop();
	
	var callMethod = function() {that.play_tt();};
	this.playing_func_id = setInterval(callMethod, this.esp_obj.isIE ? 10 : 25);
};


// function name: prepare_tt
// description : display first image of slide show and hide another images
// argument : tt_id : id of timetable
EsAudioPlayer_tt.prototype.prepare_tt = function(tt_id)
{
	var i,j;
	for (i=0; i<this.tt_id_list.length; i++) {
		for (j=0; j < esp_tt_data[this.tt_id_list[i]].img.length; j++) {
			var id = '#'+this.make_id(this.tt_id_list[i], j)+'_i';
			if (this.tt_id_list[i] == tt_id && j==0) {
				jQuery(id).fadeTo(500,1);
			} else {
				jQuery(id).stop();
				jQuery(id).fadeTo(500,0);
			}
		}
	}
};

var debug_disp=false;

// function name: play_tt
// description : playing slideshow
// argument : void
EsAudioPlayer_tt.prototype.play_tt = function()
{
	if (!this.nowplaying && this.esp_obj.play===false) return;

	var time = (new Date).getTime() - this.esp_obj.start_time;
	var next_data_pos = this.playing_tt_data_pos + 1;

	if (false) jQuery('.main_meta h2').html(next_data_pos+'  '+
		this.playing_tt_data_num+'  '+
		this.esp_obj.play+' &nbsp; '+
		(new Date).getTime()+' &nbsp; '+
		this.nowplaying+ ' &nbsp; '+
		cnt++ + ' &nbsp; ' + 
		this.esp_obj.getCurrentPosition()+ ' &nbsp; ' + 
		esp_tt_data[this.playing_tt].time[next_data_pos]+ ' &nbsp; ' + 
		time);
	if (false) {
		var ms = this.esp_obj.mySound[this.esp_obj.nowPlaying];
		jQuery('.main_meta h2').html(
			ms.duration + ' &nbsp; ' +
			ms.durationEstimate + ' &nbsp; ' +
			ms.position + ' &nbsp; ' +
			'');
		if (debug_disp) {
			jQuery('#secondary_nav').html('<pre>'+print_r(ms)+'</pre>'); debug_disp=false;
		}
	}

	if (next_data_pos >= this.playing_tt_data_num && !this.esp_obj.play) { // when current slideshow ends

		clearInterval(this.playing_func_id); // stop slideshow
		this.playing_func_id = -1;

		this.playing_tt_idx ++;
		if (this.playing_tt_idx == this.tt_id_list.length) {
			if (this.esp_obj.loop==false) {
				esplayer_autoplay_next();
				this.nowtotalplaying = false;
				return; // if loop-flag is off and every slideshow end, then finish entire process
			}
			this.playing_tt_idx = 0;
		}
			
		var that = this;
		this.nowplaying = false;
		setTimeout(function(){that.launch_tt(that.playing_tt_idx);}, 100);
		return;
	}

	var tt_id = this.playing_tt;
	if (esp_tt_data[tt_id].time[next_data_pos] < time) { // show next picture
		var duration = esp_tt_data[tt_id].duration[next_data_pos];
		jQuery('#'+this.make_id(tt_id, this.playing_tt_data_pos)+'_i').fadeTo(parseInt(duration),0);
		if (esp_tt_data[tt_id].img[next_data_pos] != "") { 
			jQuery('#'+this.make_id(tt_id, next_data_pos)+'_i').fadeTo(parseInt(duration),1);
			this.zindex++;
			jQuery('#'+this.make_id(tt_id, next_data_pos)+'_i').css('z-index', this.zindex);
		}
		this.playing_tt_data_pos = next_data_pos;
	}
};

EsAudioPlayer_tt.prototype.seek_tt = function(millisecond)
{
};

// function name: stop_slideshow
// description : stop slideshow (audio must be stopped by 
// argument : void
EsAudioPlayer_tt.prototype.stop_slideshow = function()
{
	this.prepare_tt(this.tt_id_list[0]);
	clearInterval(this.playing_func_id);
	this.playing_func_id = -1;
	this.nowplaying = false;
	this.nowtotalplaying = false;
};


// function name: make_id
// description : making the id code of div element which encloses an image 
// argument : tt_id: id of timetable  
//		i: position of image in timetable data
EsAudioPlayer_tt.prototype.make_id = function(tt_id, i)
{
	return 'img_' + this.player_id+'_'+tt_id + '_' + i;
};

// function name: makediv_for_img
// description : making div elements which enclose images
// argument : tt_id: id of timetable  
EsAudioPlayer_tt.prototype.makediv_for_img = function(tt_id)
{
	var i,id,d,n;
	this.flgButtonPressed = false;
	for (i=0; i<	esp_tt_data[tt_id].time.length; i++) {
		id = this.make_id(tt_id, i);
		d = jQuery('#'+id);
		if (d.length == 0) {
			n = this.imgs.length;
			d = jQuery('<div id="'+id+'"></div>');
			jQuery(this.box_id).append(d);
			this.imgs[n] = jQuery('#'+id);
			esp_img_loadflg[id] = new Array(2);
			esp_img_loadflg[id][0] = false;
			esp_img_loadflg[id][1] = false;
		}
	}
};


// function name: loadimage
// description : load images (with some scheduling mechanism)
// argument : void
EsAudioPlayer_tt.prototype.loadimage = function()
{
	if (this.esp_obj.play===false) { // this player is silent
		if (esp_playing_no>0) {  // another player is playing
			return;
		} else {    // no player is playing
			if (esp_count_nowloading()/*this.loading_num*/ > 3) {
				return;
			}
		}
	} else { // this player is playing
		if (this.loading_num > 0) {
			return;
		}
	}
	
	// finding img to be read next.
	var now_tt = this.playing_tt;
	var now_img = this.playing_tt_data_pos;
	var tt_num = this.tt_id_list.length;
	var tt_pos, i;
	for (tt_pos=0; tt_pos<tt_num; tt_pos++) {		//tt_pos = current_position of playing tt
		if (now_tt == this.tt_id_list[tt_pos]) {
			break;
		}
	}
	var now_tt_pos = tt_pos;

	var l;

	for (l=0;l<10000;l++) {  // avoid infinite loop
		now_img ++;
		if (now_img >= esp_tt_data[this.tt_id_list[tt_pos]].time.length) {
			now_img = 0;
			tt_pos++;
			if (tt_pos == tt_num) {
				tt_pos = 0;
			}
		}
		if (tt_pos == now_tt_pos && now_img == this.playing_tt_data_pos) {
			return;
		}
		if (esp_img_loadflg[this.make_id(this.tt_id_list[tt_pos], now_img)][0]==false) {
			this.load_an_image(this.tt_id_list[tt_pos] , now_img );
			return;
		}
	}
};




// function name: load_an_image
// description : load an image
// argument : tt_id: id of timetable; img_no: position of data of the image in timetable data
EsAudioPlayer_tt.prototype.load_an_image = function(tt_id, img_no)
{
	var id = this.make_id(tt_id, img_no);
	if (esp_img_loadflg[id][0] || esp_img_loadflg[id][1]) {
		return;
	}
	esp_img_loadflg[id][0] = true;
	esp_img_loadflg[id][1] = false;
	
	var id_img = id+"_i";
	var img = new Image();
	this.loading_num ++;
	jQuery.data(img, 'firstimg', (tt_id==this.tt_id_list[0] && img_no==0)?true:false);
	jQuery.data(img, 'obj', this);
	jQuery('#'+id).append(img);
	jQuery(img).fadeTo(0, 0);
	jQuery('#'+id).css('position','absolute');
	jQuery('#'+id).css('zindex','1');
	if (esp_tt_data[tt_id].img[img_no] == "") {
		jQuery(img).css('border', "0");
	} else {
		jQuery(img).css('border', this.esp_obj.border_img);
	}
	if (esp_tt_data[tt_id].width[img_no]) {
		jQuery.data(img,'width',  esp_tt_data[tt_id].width[img_no]);
		jQuery.data(img,'height', esp_tt_data[tt_id].height[img_no]);
	} else {
		jQuery.data(img,'width',   -1);
		jQuery.data(img,'height',  -1);
	}

	var that = this;
	jQuery(img).load(function(response, status, xhr) {
		var idi = jQuery(this).parent().attr('id');
		esp_img_loadflg[idi][1]=true;
		that.loading_num --;
		var obj=jQuery.data(this,'obj');	// obtain EsAudioPlayer_tt object
		// if image size is not specified, shrink the image so that it can be conteined in the display area.
		if (jQuery(this).data('width')==-1) {  
			var w = jQuery(this).width();
			var h = jQuery(this).height();
			var r = w/h;
			if (w > obj.box_width) {
				w =obj.box_width;
				h = w/r;
			}
			if (h > obj.box_height) {
				h = obj.box_height;
				w =h * r;
			}
			jQuery(this).width(w);
			jQuery(this).height(h);
		} else {
			jQuery(this).width(jQuery(this).data('width'));
			jQuery(this).height(jQuery(this).data('height'));
		}
		// position the image on the center of the display area.
		var left = obj.box_width/2-jQuery(img).width()/2;
		var top = obj.box_height/2-jQuery(img).height()/2;
		jQuery(this).parent().css('top',top+'px');
		jQuery(this).parent().css('left',left+'px');

		// if this image is the default image of the time table, display this.
		if (jQuery.data(this,'firstimg')==true) {
			obj.zindex++;
			jQuery(this).css('zindex',obj.zindex);
			jQuery(this).fadeTo('fast', 1);
		}
	}).error(function(){
		var idi = jQuery(this).parent().attr('id');
		esp_img_loadflg[idi][1]=true;
		this.loading_num --;
		jQuery(this).css('display','none');
		//alert('error');
	}).attr({
		id: id_img,
		src: esp_tt_data[tt_id].img[img_no]
	});
};
