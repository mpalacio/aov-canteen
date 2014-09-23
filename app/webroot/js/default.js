var validate = (function() {
	var regex = {
		// DD-MM-YYYY
		'date': /^(((0?[1-9]|[12]\d|3[01])-(0?[13578]|1[02])-((1[6-9]|[2-9]\d)\d{2}))|((0?[1-9]|[12]\d|30)-(0?[13456789]|1[012])-((1[6-9]|[2-9]\d)\d{2}))|((0?[1-9]|1\d|2[0-8])-0?2-((1[6-9]|[2-9]\d)\d{2}))|(29-0?2-((1[6-9]|[2-9]\d)(0?[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/,
		'alphanumeric_underscore': /^[a-z0-9_-]+$/,
		'email': /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/
	};

	var empty = function(value) {
		return !$.trim(value).length;
	};

	var rules = {
		alphanumeric_underscore: function(e) {
			var str = e.val();
			return (regex['alphanumeric_underscore']).test(str) | (empty(str) && !(regex['alphanumeric_underscore']).test(str));
		},
		required: function(e) {
			return !empty(e.val());
		},
		email: function(e) {
			return (regex['email']).test(e.val()) | (empty(e.val()) && !(regex['email']).test(e.val()));
		}
	};

	var error_messages = {
		alphanumeric_underscore: "This field must contain only letters, numbers, and underscores.",
		required: "This field is required.",
		email: "Invalid email address."
	};

	var render = function(e, content) {
		e.closest('.form-group').removeClass('has-error');
		e.siblings('p').remove();
		if (content === null)
			return;
		e.closest('.form-group').addClass('has-error');
		e.after(content);
	};

	var is_rule_exist = function(rule) {
		if (!(rule in rules)) {
			throw new Error("Validation rule `"+ rule +"` does not exist.");
      return false;
		}
    return true;
	}

	var clean_array = function(arr) {
		var clean_array = [];
		for (var i = 0; i < arr.length; i++) {
			var tmp = arr[i].replace(" ", "");
			if (!(/^$/.test(tmp)))
				clean_array.push(tmp);
		};
    return clean_array;
	};

	return {
		run: function(rule, e) {
			var error = false, return_message = '';
			if (typeof rule == "string") {
				rule = rule.split("|");
			}
			if (rule instanceof Array) {
				rule = clean_array(rule);
				for (var i in rule) {
					is_rule_exist(rule[i]);
					if (!(rules[rule[i]](e))) {
						error = true;
						return_message += '<p>'+ error_messages[rule[i]] +'</p>';
					}
				}
			}
			render(e, (error ? return_message : null));
			return !error;
		},

		check: function(e) {
			if (!(e instanceof jQuery))
				e = $(e);
			var rules = e.data('validate');
			if (rules === undefined)
				throw new Error("Validation Error: Element doesn't have rules.");
		},

		add_rule: function(rule_name, checking_function, error_message) {
			rules[rule_name] = function(e) {
				return checking_function(e);
			}
			error_messages[rule_name] = error_message;
		},

		submit: function(form) {
			if (!(form instanceof jQuery))
				form = $(form);
			var error = false;
			form.find(validate.tag_selector).each(function() {
				var rules = ($(this).data('validate')).split('|');
				// if it fails
				if (!validate.run(rules, $(this))) {
					error = true;
				}
			});
			return !error;
		},

		clean_field: function(e) {
			render(e, null);
		},

		set_error: function(e, error_message) {
			render(e, error_message);
		},

		tag_selector: '[data-validate]'
	};
})();

$.fn.validate = function() {
	$(this).find(validate.tag_selector).each(function() {
		validate.check($(this));
	});

	$('body').on('change focusout', validate.tag_selector, function() {
		var t = $(this), rules = (t.data('validate')).split('|');
		validate.run(rules, t);
	});

	$('body').on('focusin', validate.tag_selector, function() {
		var t = $(this);
		validate.clean_field(t);
	});

	this.on('submit', function() {
		return validate.submit(this);
	});
};

var ajax_loader = '<div class="ajax-loader"><div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div></div></div>';

$(function() {
	$(".alert").fadeTo(2000, 500).slideUp(2000, function(){
		$(".alert").alert('close');
	});
});
