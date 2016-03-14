# Contact-Form-Mailer
Mailer for contact from which uses sessions to control number of letters per time

# Short description

This is simple PHP+Ajax mailer for contact form. The main advantage of this mailer that it uses sessions to prevent sending of many letters per time from one IP. It's simple and in the same time quite efficient solution. Also it's very simple for usage.
Bootstrap css framework for view is used.

## Installation on your website (simple example)

* put files from /js/send_mail to directory with JavaScript files on your website
* put files from /mail to directory with PHP files
* include *.js files
```html
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- Custom Theme SendEmailJs -->
    <script src="js/send_mail/validator.js"></script>
    <script src="js/send_mail/send_mail.js"></script>
```
* after including of js files put the following code in script tag
```javascript
  $(document).ready(function() {
		var opts = {
		fields: {
			email_required_data: {
				subject:{id: 'subject', cyrname: 'Тема'},
				message:{id: 'message', cyrname: 'Повідомлення'}
			},
			email_data: {
				email:{id: 'email', cyrname: 'Електронна пошта'},
				name:{id: 'name', cyrname: 'Ім\'я'}
			}
		},
		url: 'mail/send.php' // path to mailer script
		};
		var SendMailObject = sendEmail(opts);
		$("#contact-form").validator().on("submit", function (event) {
			if (event.isDefaultPrevented()) {
			} else {
				event.preventDefault();
				SendMailObject.Submit(this);
			}
		});
	});
```
* do not forget to change parameter `url: 'mail/send.php'` to correct path on your website
