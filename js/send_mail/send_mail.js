$(function() {
sendEmail = function(opts) {
	function SendEmail(options) {		
		this.fields = options.fields.email_data,
		this.required_data = options.fields.email_required_data,
		this.url = options.url
	}

	SendEmail.prototype.GetFormData = function() {
		var data = {};
		var j = 0;

		for (var i in this.required_data) {
			var opt = this.required_data[i];

			var row = {cyrname:opt.cyrname, value:$("#" + opt.id).val()};
			data[i] = row;
		}

		for (var i in this.fields) {
			var opt = this.fields[i];

			var row = {cyrname:opt.cyrname, value:$("#" + opt.id).val()};
			data[i] = row;
		}

		return data;
	}

	SendEmail.prototype.Call = function(callback) {
		var data = this.GetFormData();
		var url = this.url;

		var xhr = $.ajax({
			type: "POST",
			dataType: "json",
			url: url,
			data: {
				form_data: JSON.stringify(data)
			},
			success: function (retval) {
				if (retval.status == 0) {
					alert("Повідомлення надіслано");
					callback(true);
				} else if (retval.status == 1) {
					alert("Досягнуто обмеження на кількість листів, " +
						"відпарвлених із одного комп\'ютера!\n" +
						"Скористайтеся, будь ласка, довільним поштовим сервісом на Ваш вибір"
					);
				}
				else {
					console.error(retval.status + ':' + retval.message);
				}
			},
			error: function(xhr, desc, err) {
				console.error(JSON.stringify(xhr.statusCode()));
				console.error("Details: " + desc + "\nError:" + err);
			}
		});
	}

	SendEmail.prototype.Submit = function(form) {
		this.Call(function (result) {
			if (result) {
				$(form)[0].reset();
			}
		});
	}

	SendEmailObj = new SendEmail(opts);

	return SendEmailObj;
}});