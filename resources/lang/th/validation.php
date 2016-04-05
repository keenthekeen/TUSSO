<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	'accepted' => 'The :attribute must be accepted.',
	'active_url' => 'ไม่ใช่ URL ที่ถูกต้อง',
	'after' => 'ต้องเป็นวันที่หลังจาก :date.',
	'alpha' => 'ต้องเป็นตัวอักษรเท่านั้น',
	'alpha_dash' => 'ต้องเป็นตัวอักษร ตัวเลข หรือขีด',
	'alpha_num' => 'ต้องเป็นตัวเลขหรือตัวอักษร',
	'array' => 'The :attribute must be an array.',
	'before' => 'The :attribute must be a date before :date.',
	'between' => [
		'numeric' => 'ต้องมีค่าอยู่ระหว่าง :min และ :max.',
		'file' => 'The :attribute must be between :min and :max kilobytes.',
		'string' => 'The :attribute must be between :min and :max characters.',
		'array' => 'The :attribute must have between :min and :max items.',
	],
	'boolean' => 'ต้องเป็นจริง (true) หรือ (false)',
	'confirmed' => 'ไม่ตรงกับการยืนยัน',
	'date' => 'ไม่ใช่วันที่',
	'date_format' => 'The :attribute does not match the format :format.',
	'different' => ':attribute กับ :other ต้องมีค่าต่่างกัน',
	'digits' => 'ต้องเป็นตัวเลข :digits หลัก',
	'digits_between' => 'The :attribute must be between :min and :max digits.',
	'distinct' => 'The :attribute field has a duplicate value.',
	'email' => 'ต้องเป็นอีเมล์จริง',
	'exists' => 'The selected :attribute is invalid.',
	'filled' => 'The :attribute field is required.',
	'image' => 'ต้องเป็นรูปภาพ',
	'in' => 'The selected :attribute is invalid.',
	'in_array' => 'The :attribute field does not exist in :other.',
	'integer' => 'ต้องเป็นจำนวนเต็ม',
	'ip' => 'ต้องเป็นที่อยู่ไอพี',
	'json' => 'The :attribute must be a valid JSON string.',
	'max' => [
		'numeric' => 'ต้องมีค่าไม่เกิน :max.',
		'file' => 'The :attribute may not be greater than :max kilobytes.',
		'string' => 'The :attribute may not be greater than :max characters.',
		'array' => 'The :attribute may not have more than :max items.',
	],
	'mimes' => 'ต้องเป็นไฟล์ประเภท :values.',
	'min' => [
		'numeric' => 'ต้องมีค่าอย่างน้อย :min.',
		'file' => 'The :attribute must be at least :min kilobytes.',
		'string' => 'ต้องยาวอย่างน้อย :min ตัวอักษร',
		'array' => 'The :attribute must have at least :min items.',
	],
	'not_in' => 'The selected :attribute is invalid.',
	'numeric' => 'ต้องเป็นตัวเลข',
	'present' => 'The :attribute field must be present.',
	'regex' => 'The :attribute format is invalid.',
	'required' => 'ต้องกรอก :attribute',
	'required_if' => 'The :attribute field is required when :other is :value.',
	'required_unless' => 'The :attribute field is required unless :other is in :values.',
	'required_with' => 'The :attribute field is required when :values is present.',
	'required_with_all' => 'The :attribute field is required when :values is present.',
	'required_without' => 'The :attribute field is required when :values is not present.',
	'required_without_all' => 'The :attribute field is required when none of :values are present.',
	'same' => 'The :attribute and :other must match.',
	'size' => [
		'numeric' => 'ต้องมีขนาด :size.',
		'file' => 'The :attribute must be :size kilobytes.',
		'string' => 'The :attribute must be :size characters.',
		'array' => 'The :attribute must contain :size items.',
	],
	'string' => 'The :attribute must be a string.',
	'timezone' => 'The :attribute must be a valid zone.',
	'unique' => 'The :attribute has already been taken.',
	'url' => 'The :attribute format is invalid.',

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'citizenid' => 'รหัสประจำตัวประชาชนของคุณไม่ถูกต้อง',

	'custom' => [
		'attribute-name' => [
			'rule-name' => 'custom-message',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => [],

];
