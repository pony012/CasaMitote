var submitForm = function(el, callback){
	el.submit({'callback': callback}, function(e){
		var _this = $(this);
		$.ajax({
			url: _this[0].action,
			method: _this[0].method,
			data: _this.serialize(),
			success: function(d){
				var data = JSON.parse(d);
				if(!data.error){
					e.data.callback(data);
				}
			},
		});
		_this.find(":input").val('');
		_this.find(":input")[0].focus();
		e.preventDefault();
	});
}