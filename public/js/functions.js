var __id = {};
function _id($id, $cls) {
	$cls = $cls || false;
	if($cls === true)
		__id[$id] = document.getElementById($id);
	else {
		if(typeof __id[$id] === 'undefined')
			__id[$id] = document.getElementById($id);
	}

	return __id[$id];
}
function _class( $class, $container ) {
	var elements = [];
	var _elements;

	$container = $container || document;

	_elements = $container.getElementsByClassName( $class );
	for(var i = 0; i < _elements.length; ++i)
		elements.push(_elements[i]);

	return elements;
}
function _click(_function, _object) {
	_object.addEventListener('click', _function);
}
function _change(_function, _object) {
	_object.addEventListener('change', _function);
}
function prepare_form(form_id, _callback) {
	_callback = _callback || false;
	var fields = _class('_field');
	var data = new FormData(_id(form_id));

	for(var i = 0, el; i < fields.length; ++i) {
		el = fields[i];
		if(el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
			if(el.name.substring(el.name.length - 2, el.name.length).toString() === '[]') {
				var _name = el.name.substring(0, el.name.length - 2);

				if(typeof data[_name] === 'undefined')
					data[_name] = [];
				if(el.value.length !== 0)
					data[_name].push(el.value);
			} else
				data[el.name] = el.value;
		} else if(el.tagName === 'SELECT') {
			if(el.multiple) {
				data[el.name] = [];

				for(var _i = 0; _i < el.selectedOptions.length; ++_i)
					data[el.name].push(el.selectedOptions[_i].value);
			} else
				data[el.name] = el[el.selectedIndex].value;
		}
	}

	if(false !== _callback)
		_callback(data);
}

function show_errors(errors) {
	for(var k in errors) {
		_class('field-error', _id(errors[k][0]).parentNode)[0].innerHTML = errors[k][1];
	}
}

function clear_errors() {
	var errors = _class('field-error');

	for(var k in errors) {
		errors[k].innerHTML = '';
	}
}

function buildHTTPString(data) {
	var queryString = '';

	for ( var param in data ) {
		if ( ! param )
			continue ;

		var param_value = data[param];
		if(param_value instanceof Array) {
			var q = [];
			for(var i = 0; i < param_value.length; i++)
				q.push(param + '[]=' + param_value[i]);

			queryString += q.join('&') + '&';
		} else {
			if ( typeof param_value === 'string' ) {
				param_value = param_value.trim();
			}
			if ( typeof param_value !== 'undefined') {
				queryString += param.toString() + '=' + encodeURIComponent( param_value.toString() ) + '&';
			}
		}
	}
	queryString = queryString.slice( 0, -1 );
	return queryString;
}
var XHR = {
	url: '',
	method: false,
	async: true,
	error: false,
	error_pool: {},
	before: false,
	XHR: [],
	success: false,
	beforeSend_pool: {},
	success_pool: {},
	onfinish: false,
	onfinish_pool: {},
	ontimeout: false,
	ontimeout_pool: {},
	dataType: null,
	data: null,
	timeout: null,
	timeout_pool: {},

	_: function ( url, settings ) {
		var _this = this;
		var xhr_key = _this.XHR.length;
		_this.XHR[ xhr_key ] = {};

		var urlArr = url.split( ':' );

		this.method = urlArr[ 0 ];
		this.url = url.replace( this.method + ':', '' );

		if ( settings.error )
			_this.error_pool[ xhr_key ] = settings.error;
		else
			_this.error_pool[ xhr_key ] = false;

		if ( settings.beforeSend )
			_this.beforeSend_pool[ xhr_key ] = settings.beforeSend;
		else
			_this.beforeSend_pool[ xhr_key ] = false;

		if( settings.timeout )
			_this.timeout_pool[ xhr_key ] = parseInt(settings.timeout);
		else
			_this.timeout_pool[ xhr_key ] = 5000;

		if ( settings.success )
			_this.success_pool[ xhr_key ] = settings.success;
		else
			_this.success_pool[ xhr_key ] = false;

		if( settings.onfinish )
			_this.onfinish_pool[ xhr_key ] = settings.onfinish;
		else
			_this.onfinish_pool[ xhr_key ] = false;

		if ( settings.ontimeout )
			_this.ontimeout_pool[ xhr_key ] = settings.ontimeout;
		else
			_this.ontimeout_pool[ xhr_key ] = false;

		if ( settings.data )
			_this.data = settings.data;

		if ( settings.dataType )
			_this.dataType = settings.dataType.toString().toLowerCase();

		if ( window.XMLHttpRequest )
			_this.XHR[ xhr_key ] = new XMLHttpRequest;
		else {
			try {
				_this.XHR[ xhr_key ] = ActiveXObject( "Microsoft.XMLHTTP" );
			} catch ( e ) {
				try {
					_this.XHR[ xhr_key ] = new ActiveXObject( "Msxml2.XMLHTTP" );
				} catch ( E ) {
					_this.XHR[ xhr_key ] = false;
				}
			}
		}

		if(_this.XHR[ xhr_key ] == false) {
			console.error("Can not create XHR connection...");
			return false;
		}

		if(_this.beforeSend_pool[ xhr_key ])
			_this.beforeSend_pool[ xhr_key ]();

		if ( _this.method === 'get' && _this.data )
			_this.url = _this.url.toString() + '?' + buildHTTPString( this.data );

		_this.XHR[ xhr_key ].open( _this.method.toString(), _this.url.toString(), this.async );

		_this.XHR[ xhr_key ].onload = function () {
			var response = null;
			if ( _this.XHR[ xhr_key ].status == 200 ) {
				if ( typeof _this.success_pool[ xhr_key ] == 'function' ) {
					if ( _this.dataType === 'json' ) {
						try {
							response = JSON.parse( _this.XHR[ xhr_key ].responseText );
						} catch ( e ) {
							console.error( 'Invalid JSON response', _this.XHR[ xhr_key ].responseText );
						}

						if ( response ) {
							if(_this.success_pool[ xhr_key ])
								_this.success_pool[ xhr_key ]( response );

							if(_this.onfinish_pool[ xhr_key ])
								_this.onfinish_pool[ xhr_key ]( response );
						}
					} else {
						_this.success_pool[ xhr_key ]( _this.XHR[ xhr_key ].responseText );
					}
				}
			} else {
				if ( typeof _this.error_pool[ xhr_key ] == 'function' ) {
					try {
						response = JSON.parse( _this.XHR[ xhr_key ].responseText );
					} catch ( e ) {
						response = _this.XHR[ xhr_key ].responseText;
						console.error( 'Invalid JSON response', _this.XHR[ xhr_key ].responseText );
					}
					if(_this.error_pool[ xhr_key ])
						_this.error_pool[ xhr_key ]( response );

					if(_this.onfinish_pool[ xhr_key ])
						_this.onfinish_pool[ xhr_key ]( response );
				}
			}
		};

		_this.XHR[ xhr_key ].timeout = _this.timeout_pool[ xhr_key ];

		if(typeof _this.ontimeout_pool[ xhr_key ] == 'function')
			_this.XHR[ xhr_key ].ontimeout = function() {
				_this.XHR[ xhr_key ].abort();

				if(_this.ontimeout_pool[ xhr_key ])
					_this.ontimeout_pool[ xhr_key ]();

				if(_this.onfinish_pool[ xhr_key ])
					_this.onfinish_pool[ xhr_key ]( response );
			};

		if ( this.data && this.data.__proto__.constructor.name === 'FormData' ) {
			_this.XHR[ xhr_key ].send( this.data );
		} else {
			_this.XHR[ xhr_key ].setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
			_this.XHR[ xhr_key ].send( buildHTTPString( this.data ) );
		}
	}
};

function pagenator(pagenator_container, total_pages, current_page, limit_records, pagenator_id, _function, _functions) {
	_functions = _functions || false;
	_function = _function || false;

	limit_records = limit_records || null;

	if(typeof total_pages !== 'number')
		return log_error('Invalid total pages count');

	if(typeof current_page !== 'number')
		return log_error('Invalid current page value');

	if ( ! pagenator_container)
		return log_error('Need container for pagenator');

	pagenator_container = _id(pagenator_container);
	if (typeof pagenator_container === 'undefined')
		return log_error('Invalid container id for pagenator');

	var pagenator = _id(pagenator_id);

	if( ! pagenator) {
		var _nav = _createElement('nav');
		_nav.id = pagenator_id;

		var _ul = _createElement('ul');
		_ul.classList.add('pagination');

		_appendChild(_nav, _ul);

		_appendChild(pagenator_container, _nav);
		_addClass(_nav, 'text-center')
	} else {
		_addClass(pagenator, 'text-center')
	}

	var list = _uclass('pagination', pagenator);

	list.innerHTML = '';
	var _li;
	var _a;

	_li = _createElement('li');
	_a = _createElement('a');

	_a.href = 'javascript:void(0)';
	_a.innerHTML = '<small>< First</small>';
	_a.dataset.pageAction = 'first';

	if(current_page == 1)
		_li.classList.add('active');
	else
		_a.classList.add('link');

	_appendChild(_li, _a);
	_appendChild(list, _li);

	_li = _createElement('li');
	_a = _createElement('a');

	_a.href = 'javascript:void(0)';
	_a.innerHTML = '&laquo;';
	_a.dataset.pageAction = 'prev';

	if(current_page == 1)
		_li.classList.add('active');
	else
		_a.classList.add('link');

	_appendChild(_li, _a);
	_appendChild(list, _li);

	var i;
	for(i = current_page - 3;  i <  current_page + 4 && i < total_pages + 1; ++i) {
		if(i < 1)
			continue;

		_li = _createElement('li');
		_a = _createElement('a');

		_a.href = 'javascript:void(0)';

		_a.dataset.pageAction = 'link';
		_a.dataset.page = i;

		if(i == current_page) {
			_a.innerHTML = '<small>' + i + '/' + total_pages + '</small>';
			_li.classList.add('active');
		} else {
			_a.innerHTML = i;
			_a.classList.add('link');
		}

		_appendChild(_li, _a);
		_appendChild(list, _li);
	}

	_li = _createElement('li');
	_a = _createElement('a');

	_a.href = 'javascript:void(0)';
	_a.innerHTML = '&raquo;';
	_a.dataset.pageAction = 'next';

	if(current_page >= total_pages)
		_li.classList.add('active');
	else
		_a.classList.add('link');

	_appendChild(_li, _a);
	_appendChild(list, _li);

	_li = _createElement('li');
	_a = _createElement('a');

	_a.href = 'javascript:void(0)';
	_a.innerHTML = '<small>Last ></small>';
	_a.dataset.pageAction = 'last';

	if(current_page >= total_pages)
		_li.classList.add('active');
	else
		_a.classList.add('link');

	_appendChild(_li, _a);
	_appendChild(list, _li);

	_click(change_page, _class('link', list));

	var _url = explode_url();
	var n_url = [];

	if(_functions) {
		for(i = 0; i < _url.length; ++i) {
			if(_functions[_url[i]]) {
				_functions[_url[i]](_url);
			}
		}
	}

	for(i = 0; i < _url.length; ++i) {
		if(_url[i] == '_page' || _url[i] == '_limit' || _url[i] == '_cid' || _url[i] == '_target') {
			++i;
			continue;
		}

		n_url.push(_url[i]);
	}

	_url = n_url;

	var process_url = '/' + _url.join('/');
	process_url+= '/_page/' + current_page;

	if(limit_records)
		process_url+= '/_limit/' + limit_records;

	function change_page() {
		switch (this.dataset.pageAction) {
			case 'first' : current_page = 1;
				break;
			case 'prev' : --current_page;
				break;
			case 'next' : ++current_page;
				break;
			case 'last' : current_page = total_pages;
				break;
			case 'link' : current_page = this.dataset.page;
				break;
		}

		var process_url = '/' + _url.join('/');
		process_url+= '/_page/' + current_page;

		if(limit_records)
			process_url+= '/_limit/' + limit_records;

		window.history.pushState("", "", process_url + window.location.search);
		if(_function)
			_function();
	}

	window.history.pushState("", "", process_url + window.location.search);
}