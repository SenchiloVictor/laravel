_click(function() {
	var formData = new FormData();
	formData.append('image', _id('image').files[0]);

	XHR._('post:/api/gallery/addImg', {
		data: formData,
		success: function(r) {
			loadImage(r.id);
			_id('image').value = '';
		}
	})
}, _id('addImage'));

function loadImage(id) {
	XHR._('get:/api/gallery/getImg', {
		data: {'id':id},
		dataType: 'json',
		success: function(r) {
			for(var i = 0; i < r.images.length; ++i) {
				var divWrap = document.createElement('div');
				var aLink   = document.createElement('a');
				var img     = document.createElement('img');

				divWrap.className = 'col-lg-3 col-md-4 col-xs-6 thumb';
				aLink.classList.add('thumbnail');
				img.classList.add('img-responsive');
				img.src = '/' + r.images[i]['thumb_url'];

				divWrap.appendChild(aLink);
				aLink.appendChild(img);

				_id('images-list').appendChild(divWrap);
			}
		}
	})
}

window.onload = function() {
	loadImage(-1);
}