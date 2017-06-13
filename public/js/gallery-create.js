_click(function() {
	XHR._('post:/api/gallery/create', {
		data: {'name': _id('gallery').value},
		beforeSend: function() {

		},
		success: function(r) {
			loadList(r.id);
			_id('gallery').value = '';
		},
		error: function(r) {

		},
		onfinish: function() {

		}
	});
}, _id('createGallery'))

function deleteGallery() {
	var id = this.dataset.galleryId;
	XHR._('delete:/api/gallery/delete/' + id, {
		success: function() {
			_id('gallery-' + id).remove();
		}
	})
}

function loadList(id) {
	XHR._('get:/api/gallery/list', {
		data: {id:id},
		dataType: 'json',
		success: function(r) {
			for(var i = 0; i < r.galleries.length; ++i) {
				var divWrap     = document.createElement('div');
				var divL        = document.createElement('div');
				var divR        = document.createElement('div');
				var divRDelete  = document.createElement('div');
				var divName     = document.createElement('div');
				var aLink       = document.createElement('a');
				var aLinkDelete = document.createElement('a');

				divWrap.id = 'gallery-' + r.galleries[i].id;
				divWrap.style.marginBottom = '20px';

				divWrap.classList.add('row');
				divL.classList.add('col-sm-8');
				divR.classList.add('col-sm-2');
				divRDelete.classList.add('col-sm-2');

				divWrap.appendChild(divL);
				divWrap.appendChild(divRDelete);
				divWrap.appendChild(divR);
				divRDelete.appendChild(aLinkDelete);
				divL.appendChild(divName);
				divR.appendChild(aLink);

				divName.innerText = 'Галерея : ' + r.galleries[i].name + ' Количество изображений в галереи ' + r.galleries[i].imagesCount;
				aLinkDelete.className = 'btn btn-danger btn-block';
				aLinkDelete.dataset.galleryId = r.galleries[i].id;
				aLinkDelete.onclick = deleteGallery;
				aLink.className = 'btn btn-info btn-block';
				aLink.innerText = 'Просмотр галереи';
				aLinkDelete.innerText = 'Удалить галерею';
				aLink.href = '/gallery/show/' + r.galleries[i].id;
				_id('galleries-list').appendChild(divWrap);
			}
		}
	})
}

window.onload = function() {
	loadList(-1);
}