<?php

namespace @@namespace@@\Validators;


use WebGeeker\Validation\Validation;

class MyValidate extends Validation
{
    const en = [
        // Integer type (length detection is not provided, because the sign bit of negative numbers can be confusing, you can use greater than less than comparison to do this)
        'Int' =>'“{{param}}” must be an integer',
        'IntEq' =>'“{{param}}” must be equal to {{value}}',
        'IntNe' =>'“{{param}}” cannot be equal to {{value}}',
        'IntGt' =>'“{{param}}” must be greater than {{min}}',
        'IntGe' =>'“{{param}}” must be greater than or equal to {{min}}',
        'IntLt' =>'“{{param}}” must be less than {{max}}',
        'IntLe' =>'“{{param}}” must be less than or equal to {{max}}',
        'IntGtLt' =>'“{{param}}” must be greater than {{min}} and less than {{max}}',
        'IntGeLe' =>'“{{param}}” must be greater than or equal to {{min}} less than or equal to {{max}}',
        'IntGtLe' =>'“{{param}}” must be greater than {{min}} less than or equal to {{max}}',
        'IntGeLt' =>'“{{param}}” must be greater than or equal to {{min}} and less than {{max}}',
        'IntIn' =>'“{{param}}” can only take these values: {{valueList}}',
        'IntNotIn' =>'“{{param}}” cannot take these values: {{valueList}}',

        // Floating point type (double is used internally for processing)
        'Float' =>'“{{param}}” must be a floating point number',
        'FloatGt' =>'“{{param}}” must be greater than {{min}}',
        'FloatGe' =>'“{{param}}” must be greater than or equal to {{min}}',
        'FloatLt' =>'“{{param}}” must be less than {{max}}',
        'FloatLe' =>'“{{param}}” must be less than or equal to {{max}}',
        'FloatGtLt' =>'“{{param}}” must be greater than {{min}} and less than {{max}}',
        'FloatGeLe' =>'“{{param}}” must be greater than or equal to {{min}} less than or equal to {{max}}',
        'FloatGtLe' =>'“{{param}}” must be greater than {{min}} less than or equal to {{max}}',
        'FloatGeLt' =>'“{{param}}” must be greater than or equal to {{min}} and less than {{max}}',

        // bool type
        'Bool' =>'“{{param}}” must be bool type (true or false)', // Ignore case
        'BoolSmart' =>'“{{param}}” can only take these values: true, false, 1, 0, yes, no, y, n (ignoring case)',
        'BoolTrue' =>'“{{param}}” must be true',
        'BoolFalse' =>'“{{param}}” must be false',
        'BoolSmartTrue' =>'“{{param}}” can only take these values: true, 1, yes, y (ignoring case)',
        'BoolSmartFalse' =>'“{{param}}” can only take these values: false, 0, no, n (ignoring case)',

        // string
        'Str' =>'“{{param}}” must be a string',
        'StrEq' =>'“{{param}}” must be equal to “{{value}}”',
        'StrEqI' =>'“{{param}}” must be equal to “{{value}}” (ignoring case)',
        'StrNe' =>'“{{param}}” cannot be equal to “{{value}}”',
        'StrNeI' =>'“{{param}}” cannot be equal to “{{value}}” (ignoring case)',
        'StrIn' =>'“{{param}}” can only take these values: {{valueList}}',
        'StrInI' =>'“{{param}}” can only take these values: {{valueList}} (ignoring case)',
        'StrNotIn' =>'“{{param}}” cannot take these values: {{valueList}}',
        'StrNotInI' =>'“{{param}}” cannot take these values: {{valueList}} (ignoring case)',
        // todo StrSame:var detects whether a parameter is equal to another parameter, for example, password2 must be equal to password
        'StrLen' =>'“{{param}}” length must be equal to {{length}}', // string length
        'StrLenGe' =>'“{{param}}” length must be greater than or equal to {{min}}',
        'StrLenLe' =>'“{{param}}” length must be less than or equal to {{max}}',
        'StrLenGeLe' =>'“{{param}}” length must be between {{min}}-{{max}}', // string length
        'ByteLen' =>'“{{param}}” length (bytes) must be equal to {{length}}', // string length
        'ByteLenGe' =>'“{{param}}” length (bytes) must be greater than or equal to {{min}}',
        'ByteLenLe' =>'“{{param}}” length (bytes) must be less than or equal to {{max}}',
        'ByteLenGeLe' =>'“{{param}}” length (bytes) must be between {{min}}-{{max}}', // string length
        'Letters' =>'“{{param}}” can only contain letters',
        'Alphabet' =>'“{{param}}” can only contain letters', // same as Letters
        'Numbers' =>'“{{param}}” can only be pure numbers',
        'Digits' =>'“{{param}}” can only be pure numbers', // same as Numbers
        'LettersNumbers' =>'“{{param}}” can only contain letters and numbers',
        'Numeric' =>'“{{param}}” must be a numeric value', // Generally used for processing large numbers (numbers that exceed the double representation range, usually represented by strings), if it is a number within the normal range , You can use'Int' or'Float' to detect
        'VarName' =>'“{{param}}” can only contain letters, numbers and underscores, and start with a letter or underscore',
        'Email' =>'“{{param}}” is not a legal email',
        'Url' =>'“{{param}}” is not a legal Url address',
        'Ip' =>'“{{param}}” is not a legal IP address',
        'Mac' =>'“{{param}}” is not a valid MAC address',
        'Regexp' =>'“{{param}}” does not match the regular expression “{{regexp}}”', // Perl regular expression matches. Modifiers are not currently supported. http://www.rexegg.com/ regex-modifiers.html

        // Array. How to detect the length of the array is 0
        'Arr' =>'“{{param}}” must be an array',
        'ArrLen' =>'“{{param}}” length must be equal to {{length}}',
        'ArrLenGe' =>'“{{param}}” length must be greater than or equal to {{min}}',
        'ArrLenLe' =>'“{{param}}” length must be less than or equal to {{max}}',
        'ArrLenGeLe' =>'“{{param}}” length must be between {{min}} ~ {{max}}',

        // object
        'Obj' =>'“{{param}}” must be an object',

        // file
        'File' =>'“{{param}}” must be a file',
        'FileMaxSize' =>'“{{param}}” must be a file, and the file size does not exceed {{size}}',
        'FileMinSize' =>'“{{param}}” must be a file, and the file size is not less than {{size}}',
        'FileImage' =>'“{{param}}” must be an image',
        'FileVideo' =>'“{{param}}” must be a video file',
        'FileAudio' =>'“{{param}}” must be an audio file',
        'FileMimes' =>'“{{param}}” must be files of these MIME types: {{mimes}}',

        // Date & Time
        'Date' =>'“{{param}}” must conform to the date format YYYY-MM-DD',
        'DateFrom' =>'“{{param}}” must not be earlier than {{from}}',
        'DateTo' =>'“{{param}}” must be no later than {{to}}',
        'DateFromTo' =>'“{{param}}” must be between {{from}} ~ {{to}}',
        'DateTime' =>'“{{param}}” must conform to the date and time format YYYY-MM-DD HH:mm:ss',
        'DateTimeFrom' =>'“{{param}}” must not be earlier than {{from}}',
        'DateTimeTo' =>'“{{param}}” must be earlier than {{to}}',
        'DateTimeFromTo' =>'“{{param}}” must be between {{from}} ~ {{to}}',

        // other
        'Required' =>'Required to provide “{{param}}”',
    ];

    const zh = [
        // 整型（不提供length检测,因为负数的符号位会让人混乱, 可以用大于小于比较来做到这一点）
        'Int' => '“{{param}}”必须是整数',
        'IntEq' => '“{{param}}”必须等于 {{value}}',
        'IntNe' => '“{{param}}”不能等于 {{value}}',
        'IntGt' => '“{{param}}”必须大于 {{min}}',
        'IntGe' => '“{{param}}”必须大于等于 {{min}}',
        'IntLt' => '“{{param}}”必须小于 {{max}}',
        'IntLe' => '“{{param}}”必须小于等于 {{max}}',
        'IntGtLt' => '“{{param}}”必须大于 {{min}} 小于 {{max}}',
        'IntGeLe' => '“{{param}}”必须大于等于 {{min}} 小于等于 {{max}}',
        'IntGtLe' => '“{{param}}”必须大于 {{min}} 小于等于 {{max}}',
        'IntGeLt' => '“{{param}}”必须大于等于 {{min}} 小于 {{max}}',
        'IntIn' => '“{{param}}”只能取这些值: {{valueList}}',
        'IntNotIn' => '“{{param}}”不能取这些值: {{valueList}}',

        // 浮点型（内部一律使用double来处理）
        'Float' => '“{{param}}”必须是浮点数',
        'FloatGt' => '“{{param}}”必须大于 {{min}}',
        'FloatGe' => '“{{param}}”必须大于等于 {{min}}',
        'FloatLt' => '“{{param}}”必须小于 {{max}}',
        'FloatLe' => '“{{param}}”必须小于等于 {{max}}',
        'FloatGtLt' => '“{{param}}”必须大于 {{min}} 小于 {{max}}',
        'FloatGeLe' => '“{{param}}”必须大于等于 {{min}} 小于等于 {{max}}',
        'FloatGtLe' => '“{{param}}”必须大于 {{min}} 小于等于 {{max}}',
        'FloatGeLt' => '“{{param}}”必须大于等于 {{min}} 小于 {{max}}',

        // bool型
        'Bool' => '“{{param}}”必须是bool型(true or false)', // 忽略大小写
        'BoolSmart' => '“{{param}}”只能取这些值: true, false, 1, 0, yes, no, y, n（忽略大小写）',
        'BoolTrue' => '“{{param}}”必须为true',
        'BoolFalse' => '“{{param}}”必须为false',
        'BoolSmartTrue' => '“{{param}}”只能取这些值: true, 1, yes, y（忽略大小写）',
        'BoolSmartFalse' => '“{{param}}”只能取这些值: false, 0, no, n（忽略大小写）',

        // 字符串
        'Str' => '“{{param}}”必须是字符串',
        'StrEq' => '“{{param}}”必须等于"{{value}}"',
        'StrEqI' => '“{{param}}”必须等于"{{value}}"（忽略大小写）',
        'StrNe' => '“{{param}}”不能等于"{{value}}"',
        'StrNeI' => '“{{param}}”不能等于"{{value}}"（忽略大小写）',
        'StrIn' => '“{{param}}”只能取这些值: {{valueList}}',
        'StrInI' => '“{{param}}”只能取这些值: {{valueList}}（忽略大小写）',
        'StrNotIn' => '“{{param}}”不能取这些值: {{valueList}}',
        'StrNotInI' => '“{{param}}”不能取这些值: {{valueList}}（忽略大小写）',
        // todo StrSame:var 检测某个参数是否等于另一个参数, 比如password2要等于password
        'StrLen' => '“{{param}}”长度必须等于 {{length}}', // 字符串长度
        'StrLenGe' => '“{{param}}”长度必须大于等于 {{min}}',
        'StrLenLe' => '“{{param}}”长度必须小于等于 {{max}}',
        'StrLenGeLe' => '“{{param}}”长度必须在 {{min}} - {{max}} 之间', // 字符串长度
        'ByteLen' => '“{{param}}”长度（字节）必须等于 {{length}}', // 字符串长度
        'ByteLenGe' => '“{{param}}”长度（字节）必须大于等于 {{min}}',
        'ByteLenLe' => '“{{param}}”长度（字节）必须小于等于 {{max}}',
        'ByteLenGeLe' => '“{{param}}”长度（字节）必须在 {{min}} - {{max}} 之间', // 字符串长度
        'Letters' => '“{{param}}”只能包含字母',
        'Alphabet' => '“{{param}}”只能包含字母', // 同Letters
        'Numbers' => '“{{param}}”只能是纯数字',
        'Digits' => '“{{param}}”只能是纯数字', // 同Numbers
        'LettersNumbers' => '“{{param}}”只能包含字母和数字',
        'Numeric' => '“{{param}}”必须是数值', // 一般用于大数处理（超过double表示范围的数,一般会用字符串来表示）, 如果是正常范围内的数, 可以使用'Int'或'Float'来检测
        'VarName' => '“{{param}}”只能包含字母、数字和下划线，并且以字母或下划线开头',
        'Email' => '“{{param}}”不是合法的email',
        'Url' => '“{{param}}”不是合法的Url地址',
        'Ip' => '“{{param}}”不是合法的IP地址',
        'Mac' => '“{{param}}”不是合法的MAC地址',
        'Regexp' => '“{{param}}”不匹配正则表达式“{{regexp}}”', // Perl正则表达式匹配. 目前不支持modifiers. http://www.rexegg.com/regex-modifiers.html

        // 数组. 如何检测数组长度为0
        'Arr' => '“{{param}}”必须是数组',
        'ArrLen' => '“{{param}}”长度必须等于 {{length}}',
        'ArrLenGe' => '“{{param}}”长度必须大于等于 {{min}}',
        'ArrLenLe' => '“{{param}}”长度必须小于等于 {{max}}',
        'ArrLenGeLe' => '“{{param}}”长度必须在 {{min}} ~ {{max}} 之间',

        // 对象
        'Obj' => '“{{param}}”必须是对象',

        // 文件
        'File' => '“{{param}}”必须是文件',
        'FileMaxSize' => '“{{param}}”必须是文件, 且文件大小不超过{{size}}',
        'FileMinSize' => '“{{param}}”必须是文件, 且文件大小不小于{{size}}',
        'FileImage' => '“{{param}}”必须是图片',
        'FileVideo' => '“{{param}}”必须是视频文件',
        'FileAudio' => '“{{param}}”必须是音频文件',
        'FileMimes' => '“{{param}}”必须是这些MIME类型的文件:{{mimes}}',

        // Date & Time
        'Date' => '“{{param}}”必须符合日期格式YYYY-MM-DD',
        'DateFrom' => '“{{param}}”不得早于 {{from}}',
        'DateTo' => '“{{param}}”不得晚于 {{to}}',
        'DateFromTo' => '“{{param}}”必须在 {{from}} ~ {{to}} 之间',
        'DateTime' => '“{{param}}”必须符合日期时间格式YYYY-MM-DD HH:mm:ss',
        'DateTimeFrom' => '“{{param}}”不得早于 {{from}}',
        'DateTimeTo' => '“{{param}}”必须早于 {{to}}',
        'DateTimeFromTo' => '“{{param}}”必须在 {{from}} ~ {{to}} 之间',

        // 其它
        'Required' => '必须提供“{{param}}”',
    ];

    const th = [
        'Int' =>'“{{param}}” ต้องเป็นจำนวนเต็ม',
        'IntEq' =>'“{{param}}” ต้องเท่ากับ {{value}}',
        'IntNe' =>'“{{param}}” ไม่สามารถเท่ากับ {{value}}',
        'IntGt' =>'“{{param}}” ต้องมากกว่า {{min}}',
        'IntGe' =>'“{{param}}” ต้องมากกว่าหรือเท่ากับ {{min}}',
        'IntLt' =>'“{{param}}” ต้องน้อยกว่า {{max}}',
        'IntLe' =>'“{{param}}” ต้องน้อยกว่าหรือเท่ากับ {{max}}',
        'IntGtLt' =>'“{{param}}” ต้องมากกว่า {{min}} และน้อยกว่า {{max}}',
        'IntGeLe' =>'“{{param}}” ต้องมากกว่าหรือเท่ากับ {{min}} น้อยกว่าหรือเท่ากับ {{max}}',
        'IntGtLe' =>'“{{param}}” ต้องมากกว่า {{min}} น้อยกว่าหรือเท่ากับ {{max}}',
        'IntGeLt' =>'“{{param}}” ต้องมากกว่าหรือเท่ากับ {{min}} และน้อยกว่า {{max}}',
        'IntIn' =>'“{{param}}” รับได้เฉพาะค่าเหล่านี้: {{valueList}}',
        'IntNotIn' =>'“{{param}}” ไม่สามารถใช้ค่าเหล่านี้: {{valueList}}',

        // ประเภททศนิยม (ใช้ภายในสองเท่าสำหรับการประมวลผล)
        'Float' =>'“{{param}}” ต้องเป็นเลขทศนิยม',
        'FloatGt' =>'“{{param}}” ต้องมากกว่า {{min}}',
        'FloatGe' =>'“{{param}}” ต้องมากกว่าหรือเท่ากับ {{min}}',
        'FloatLt' =>'“{{param}}” ต้องน้อยกว่า {{max}}',
        'FloatLe' =>'“{{param}}” ต้องน้อยกว่าหรือเท่ากับ {{max}}',
        'FloatGtLt' =>'“{{param}}” ต้องมากกว่า {{min}} และน้อยกว่า {{max}}',
        'FloatGeLe' =>'“{{param}}” ต้องมากกว่าหรือเท่ากับ {{min}} น้อยกว่าหรือเท่ากับ {{max}}',
        'FloatGtLe' =>'“{{param}}” ต้องมากกว่า {{min}} น้อยกว่าหรือเท่ากับ {{max}}',
        'FloatGeLt' =>'“{{param}}” ต้องมากกว่าหรือเท่ากับ {{min}} และน้อยกว่า {{max}}',

        // ประเภทบูล
        'Bool' =>'“{{param}}” ต้องเป็นประเภทบูล (จริงหรือเท็จ)', // ไม่สนใจตัวพิมพ์
        'BoolSmart' =>'“{{param}}” รับได้เฉพาะค่าเหล่านี้: true, false, 1, 0, yes, no, y, n (ละเว้นตัวพิมพ์)',
        'BoolTrue' =>'“{{param}}” ต้องเป็น true',
        'BoolFalse' =>'“{{param}}” ต้องเป็นเท็จ',
        'BoolSmartTrue' =>'“{{param}}” รับได้เฉพาะค่าเหล่านี้: true, 1, yes, y (ละเว้นตัวพิมพ์)',
        'BoolSmartFalse' =>'“{{param}}” รับได้เฉพาะค่าเหล่านี้: false, 0, no, n (ละเว้นตัวพิมพ์)',

        // สตริง
        'Str' =>'“{{param}}” ต้องเป็นสตริง',
        'StrEq' =>'“{{param}}” ต้องเท่ากับ "{{value}}"',
        'StrEqI' =>'“{{param}}” ต้องเท่ากับ "{{value}}" (ไม่สนใจตัวพิมพ์เล็กและตัวพิมพ์ใหญ่)',
        'StrNe' =>'“{{param}}” ไม่สามารถเท่ากับ "{{value}}"',
        'StrNeI' =>'“{{param}}” ไม่สามารถเท่ากับ "{{value}}" (ไม่สนใจตัวพิมพ์เล็กและตัวพิมพ์ใหญ่)',
        'StrIn' =>'“{{param}}” รับได้เฉพาะค่าเหล่านี้: {{valueList}}',
        'StrInI' =>'“{{param}}” รับได้เฉพาะค่าเหล่านี้: {{valueList}} (ละเว้นตัวพิมพ์)',
        'StrNotIn' =>'“{{param}}” ไม่สามารถใช้ค่าเหล่านี้: {{valueList}}',
        'StrNotInI' =>'“{{param}}” ไม่สามารถใช้ค่าเหล่านี้ได้: {{valueList}} (ละเว้นตัวพิมพ์เล็กและตัวพิมพ์ใหญ่)',
        // todo StrSame:var ตรวจพบว่าพารามิเตอร์เท่ากับพารามิเตอร์อื่นหรือไม่ ตัวอย่างเช่น password2 ต้องเท่ากับรหัสผ่าน
        'StrLen' =>'“{{param}}” ความยาวต้องเท่ากับ {{length}}', // ความยาวของสตริง
        'StrLenGe' =>'“{{param}}” ความยาวต้องมากกว่าหรือเท่ากับ {{min}}',
        'StrLenLe' =>'“{{param}}” ความยาวต้องน้อยกว่าหรือเท่ากับ {{max}}',
        'StrLenGeLe' =>'“{{param}}” ความยาวต้องอยู่ระหว่าง {{min}}-{{max}}', // ความยาวของสตริง
        'ByteLen' =>'“{{param}}” ความยาว (ไบต์) ต้องเท่ากับ {{length}}', // ความยาวของสตริง
        'ByteLenGe' =>'“{{param}}” ความยาว (ไบต์) ต้องมากกว่าหรือเท่ากับ {{min}}',
        'ByteLenLe' =>'“{{param}}” ความยาว (ไบต์) ต้องน้อยกว่าหรือเท่ากับ {{max}}',
        'ByteLenGeLe' =>'“{{param}}” ความยาว (ไบต์) ต้องอยู่ระหว่าง {{min}}-{{max}}', // ความยาวของสตริง
        'Letters' =>'“{{param}}” มีได้เฉพาะตัวอักษร',
        'Alphabet' =>'“{{param}}” มีได้เฉพาะตัวอักษร', // เหมือนกับ Letters
        'Numbers' =>'“{{param}}” เป็นตัวเลขได้เท่านั้น',
        'Digits' =>'“{{param}}” เป็นตัวเลขได้เท่านั้น', // เหมือนกับ Numbers
        'LettersNumbers' =>'“{{param}}” มีได้เฉพาะตัวอักษรและตัวเลขเท่านั้น',
        'Numeric' =>'“{{param}}” ต้องเป็นค่าตัวเลข', // โดยทั่วไปใช้สำหรับการประมวลผลจำนวนมาก (ตัวเลขที่เกินช่วงการแสดงคู่ มักจะแสดงด้วยสตริง) หากเป็นตัวเลขภายใน ช่วงปกติ คุณสามารถใช้ 'Int' หรือ 'Float' เพื่อตรวจจับ
        'VarName' =>'“{{param}}” มีได้เฉพาะตัวอักษร ตัวเลข และขีดล่าง และขึ้นต้นด้วยตัวอักษรหรือขีดล่าง',
        'Email' =>'“{{param}}” ไม่ใช่อีเมลทางกฎหมาย',
        'Url' =>'“{{param}}” ไม่ใช่ที่อยู่ URL ตามกฎหมาย',
        'Ip' =>'“{{param}}” ไม่ใช่ที่อยู่ IP ตามกฎหมาย',
        'Mac' =>'“{{param}}” ไม่ใช่ที่อยู่ MAC ที่ถูกต้อง',
        'Regexp' =>'“{{param}}” ไม่ตรงกับนิพจน์ทั่วไป "{{regexp}}"', // จับคู่นิพจน์ทั่วไปของ Perl ตัวแก้ไขไม่ได้รับการสนับสนุนในขณะนี้ http://www.rexegg.com / regex-modifiers.html

        // Array วิธีตรวจสอบความยาวของอาร์เรย์คือ0
        'Arr' =>'“{{param}}” ต้องเป็นอาร์เรย์',
        'ArrLen' =>'“{{param}}” ความยาวต้องเท่ากับ {{length}}',
        'ArrLenGe' =>'“{{param}}” ความยาวต้องมากกว่าหรือเท่ากับ {{min}}',
        'ArrLenLe' =>'“{{param}}” ความยาวต้องน้อยกว่าหรือเท่ากับ {{max}}',
        'ArrLenGeLe' =>'“{{param}}” ต้องมีความยาวระหว่าง {{min}} ~ {{max}}',

        // วัตถุ
        'Obj' =>'“{{param}}” ต้องเป็นวัตถุ',

        // ไฟล์
        'File' =>'“{{param}}” ต้องเป็นไฟล์',
        'FileMaxSize' =>'“{{param}}” ต้องเป็นไฟล์ และขนาดไฟล์ไม่เกิน {{size}}',
        'FileMinSize' =>'“{{param}}” ต้องเป็นไฟล์ และมีขนาดไฟล์ไม่ต่ำกว่า {{size}}',
        'FileImage' =>'“{{param}}” ต้องเป็นรูปภาพ',
        'FileVideo' =>'“{{param}}” ต้องเป็นไฟล์วิดีโอ',
        'FileAudio' =>'“{{param}}” ต้องเป็นไฟล์เสียง',
        'FileMimes' =>'“{{param}}” ต้องเป็นไฟล์ประเภท MIME เหล่านี้: {{mimes}}',

        // วันเวลา
        'Date' =>'“{{param}}” ต้องเป็นไปตามรูปแบบวันที่ YYYY-MM-DD',
        'DateFrom' =>'“{{param}}” ต้องไม่เก่ากว่า {{from}}',
        'DateTo' =>'“{{param}}” ต้องไม่ช้ากว่า {{to}}',
        'DateFromTo' =>'“{{param}}” ต้องอยู่ระหว่าง {{from}} ~ {{to}}',
        'DateTime' =>'“{{param}}” ต้องเป็นไปตามรูปแบบวันที่และเวลา YYYY-MM-DD HH:mm:ss',
        'DateTimeFrom' =>'“{{param}}” ต้องไม่เก่ากว่า {{from}}',
        'DateTimeTo' =>'“{{param}}” ต้องเก่ากว่า {{to}}',
        'DateTimeFromTo' =>'“{{param}}” ต้องอยู่ระหว่าง {{from}} ~ {{to}}',

        // อื่นๆ
        'Required' =>'จำเป็นต้องระบุ “{{param}}”',
    ];

    const la = [
        'Int' => '“{{param}}” ຕ້ອງເປັນຈຳນວນເຕັມ',
        'IntEq' => '“{{param}}” ຕ້ອງເທົ່າກັບ {{value}}',
        'IntNe' => '“{{param}}” ບໍ່ສາມາດເທົ່າກັບ {{value}}',
        'IntGt' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ {{min}}',
        'IntGe' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ ຫຼືເທົ່າກັບ {{min}}',
        'IntLt' => '“{{param}}” ຕ້ອງໜ້ອຍກວ່າ {{max}}',
        'IntLe' => '“{{param}}” ຕ້ອງໜ້ອຍກວ່າ ຫຼືເທົ່າກັບ {{max}}',
        'IntGtLt' => '“{{param}}” ຈະຕ້ອງໃຫຍ່ກວ່າ {{min}} ແລະນ້ອຍກວ່າ {{max}}',
        'IntGeLe' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ ຫຼືເທົ່າກັບ {{min}} ໜ້ອຍກວ່າ ຫຼືເທົ່າກັບ {{max}}',
        'IntGtLe' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ {{min}} ໜ້ອຍກວ່າ ຫຼືເທົ່າກັບ {{max}}',
        'IntGeLt' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ ຫຼືເທົ່າກັບ {{min}} ໜ້ອຍກວ່າ {{max}}',
        'IntIn' => '“{{param}}” ສາມາດເອົາຄ່າເຫຼົ່ານີ້ໄດ້ເທົ່ານັ້ນ: {{valueList}}',
        'IntNotIn' => '“{{param}}” ບໍ່ສາມາດເອົາຄ່າເຫຼົ່ານີ້: {{valueList}}',

        'float' => '“{{param}}” ຕ້ອງເປັນຕົວເລກຈຸດລອຍ',
        'FloatGt' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ {{min}}',
        'FloatGe' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ ຫຼືເທົ່າກັບ {{min}}',
        'FloatLt' => '“{{param}}” ຕ້ອງໜ້ອຍກວ່າ {{max}}',
        'FloatLe' => '“{{param}}” ຕ້ອງໜ້ອຍກວ່າ ຫຼືເທົ່າກັບ {{max}}',
        'FloatGtLt' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ {{min}} ແລະນ້ອຍກວ່າ {{max}}',
        'FloatGeLe' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ ຫຼືເທົ່າກັບ {{min}} ໜ້ອຍກວ່າ ຫຼືເທົ່າກັບ {{max}}',
        'FloatGtLe' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ {{min}} ໜ້ອຍກວ່າ ຫຼືເທົ່າກັບ {{max}}',
        'FloatGeLt' => '“{{param}}” ຕ້ອງໃຫຍ່ກວ່າ ຫຼືເທົ່າກັບ {{min}} ໜ້ອຍກວ່າ {{max}}',

        'Bool' => '“{{param}}” ຕ້ອງເປັນ bool (ຖືກ ຫຼື ຜິດ)', // ignore case
        'BoolSmart' => '“{{param}}” ສາມາດເອົາຄ່າເຫຼົ່ານີ້ໄດ້ເທົ່ານັ້ນ: true, false, 1, 0, yes, no, y, n (ignoring case)',
        'BoolTrue' => '“{{param}}” ຕ້ອງເປັນຄວາມຈິງ',
        'BoolFalse' => '“{{param}}” ຕ້ອງເປັນຜິດ',
        'BoolSmartTrue' => '“{{param}}” ສາມາດເອົາຄ່າເຫຼົ່ານີ້ໄດ້ເທົ່ານັ້ນ: true, 1, yes, y (ignoring case)',
        'BoolSmartFalse' => '“{{param}}” ສາມາດເອົາຄ່າເຫຼົ່ານີ້ໄດ້ເທົ່ານັ້ນ: false, 0, no, n (ignoring case)',

        'str' => '“{{param}}” ຕ້ອງເປັນສະຕຣິງ',
        'StrEq' => '“{{param}}” ຕ້ອງເທົ່າກັບ “{{value}}”',
        'StrEqI' => '“{{param}}” ຕ້ອງເທົ່າກັບ “{{value}}” (ບໍ່ສົນໃຈກໍລະນີ)',
        'StrNe' => '“{{param}}” ບໍ່ສາມາດເທົ່າກັບ “{{value}}”',
        'StrNeI' => '“{{param}}” ບໍ່ສາມາດເທົ່າກັບ “{{value}}” (ບໍ່ສົນໃຈກໍລະນີ)',
        'StrIn' => '“{{param}}” ສາມາດເອົາຄ່າເຫຼົ່ານີ້ໄດ້ເທົ່ານັ້ນ: {{valueList}}',
        'StrInI' => '“{{param}}” ສາມາດເອົາຄ່າເຫຼົ່ານີ້ໄດ້ເທົ່ານັ້ນ: {{valueList}} (ບໍ່ສົນໃຈກໍລະນີ)',
        'StrNotIn' => '“{{param}}” ບໍ່ສາມາດເອົາຄ່າເຫຼົ່ານີ້: {{valueList}}',
        'StrNotInI' => '“{{param}}” ບໍ່ສາມາດເອົາຄ່າເຫຼົ່ານີ້: {{valueList}} (ບໍ່ສົນໃຈກໍລະນີ)',
        'StrLen' => '“{{param}}” ຄວາມຍາວຕ້ອງເທົ່າກັບ {{length}}', // ຄວາມຍາວຂອງສະຕຣິງ
        'StrLenGe' => 'ຄວາມຍາວຂອງ “{{param}}” ຈະຕ້ອງໃຫຍ່ກວ່າ ຫຼືເທົ່າກັບ {{min}}',
        'StrLenLe' => '“{{param}}” ຄວາມຍາວຕ້ອງໜ້ອຍກວ່າ ຫຼືເທົ່າກັບ {{max}}',
        'StrLenGeLe' => '“{{param}}” ຄວາມຍາວຕ້ອງຢູ່ລະຫວ່າງ {{min}} - {{max}}', // ຄວາມຍາວຂອງສະຕຣິງ
        'ByteLen' => '“{{param}}” ຄວາມຍາວ (ໄບຕ໌) ຕ້ອງເທົ່າກັບ {{length}}', // ຄວາມຍາວສະຕຣິງ
        'ByteLenGe' => '“{{param}}” ຄວາມຍາວ (ໄບຕ໌) ຈະຕ້ອງໃຫຍ່ກວ່າ ຫຼືເທົ່າກັບ {{min}}',
        'ByteLenLe' => '“{{param}}” ຄວາມຍາວ (ໄບຕ໌) ຈະຕ້ອງໜ້ອຍກວ່າ ຫຼືເທົ່າກັບ {{max}}',
        'ByteLenGeLe' => '“{{param}}” ຄວາມຍາວ (ໄບຕ໌) ຕ້ອງຢູ່ລະຫວ່າງ {{min}} - {{max}}', // ຄວາມຍາວສະຕຣິງ
        'Letters' => '“{{param}}” ສາມາດມີຕົວອັກສອນເທົ່ານັ້ນ',
        'Alphabet' => '“{{param}}” ສາມາດມີຕົວອັກສອນເທົ່ານັ້ນ',
        'Numbers' => '“{{param}}” ສາມາດເປັນຕົວເລກບໍລິສຸດເທົ່ານັ້ນ',
        'Digits' => '“{{param}}” ສາມາດເປັນຕົວເລກທີ່ບໍລິສຸດເທົ່ານັ້ນ',
        'LettersNumbers' => '“{{param}}” ສາມາດບັນຈຸໄດ້ແຕ່ຕົວໜັງສື ແລະຕົວເລກເທົ່ານັ້ນ',
        'Numeric' => '“{{param}}” ຕ້ອງເປັນຄ່າຕົວເລກ',
        'VarName' => '“{{param}}” ສາມາດບັນຈຸໄດ້ແຕ່ຕົວໜັງສື, ຕົວເລກ ແລະ ຂີດກ້ອງ, ແລະເລີ່ມຕົ້ນດ້ວຍຕົວອັກສອນ ຫຼື ຂີດກ້ອງ',
        'Email' => '“{{param}}” ບໍ່ແມ່ນອີເມວທີ່ຖືກຕ້ອງ',
        'Url' => '“{{param}}” ບໍ່ແມ່ນທີ່ຢູ່ Url ທີ່ຖືກຕ້ອງ',
        'IP' => '“{{param}}” ບໍ່ແມ່ນທີ່ຢູ່ IP ທີ່ຖືກຕ້ອງ',
        'Mac' => '“{{param}}” ບໍ່ແມ່ນທີ່ຢູ່ MAC ທີ່ຖືກຕ້ອງ',
        'Regexp' => '“{{param}}” ບໍ່ກົງກັບສຳນວນປົກກະຕິ “{{regexp}}”',

        'Arr' => '“{{param}}” ຕ້ອງເປັນອາເຣ',
        'ArrLen' => '“{{param}}” ຄວາມຍາວຕ້ອງເທົ່າກັບ {{length}}',
        'ArrLenGe' => 'ຄວາມຍາວຂອງ “{{param}}” ຈະຕ້ອງໃຫຍ່ກວ່າ ຫຼືເທົ່າກັບ {{min}}',
        'ArrLenLe' => '“{{param}}” ຄວາມຍາວຕ້ອງໜ້ອຍກວ່າ ຫຼືເທົ່າກັບ {{max}}',
        'ArrLenGeLe' => '“{{param}}” ຄວາມຍາວຕ້ອງຢູ່ລະຫວ່າງ {{min}} ~ {{max}}',

        // ວັດຖຸ
        'obj' => '“{{param}}” ຕ້ອງເປັນວັດຖຸ',

        // ເອກະສານ
        'File' => '“{{param}}” ຕ້ອງເປັນໄຟລ໌',
        'FileMaxSize' => '“{{param}}” ຕ້ອງເປັນໄຟລ໌, ແລະຂະໜາດໄຟລ໌ບໍ່ເກີນ {{size}}',
        'FileMinSize' => '“{{param}}” ຕ້ອງເປັນໄຟລ໌, ແລະຂະໜາດໄຟລ໌ບໍ່ໜ້ອຍກວ່າ {{size}}',
        'FileImage' => '“{{param}}” ຕ້ອງເປັນຮູບ',
        'FileVideo' => '“{{param}}” ຕ້ອງເປັນໄຟລ໌ວິດີໂອ',
        'FileAudio' => '“{{param}}” ຕ້ອງເປັນໄຟລ໌ສຽງ',
        'FileMimes' => '“{{param}}” ຕ້ອງເປັນໄຟລ໌ປະເພດ MIME ເຫຼົ່ານີ້: {{mimes}}',

        'Date' => '“{{param}}” ຕ້ອງສອດຄ່ອງກັບຮູບແບບວັນທີ YYYY-MM-DD',
        'DateFrom' => '“{{param}}” ຈະຕ້ອງບໍ່ໄວກວ່າ {{from}}',
        'DateTo' => '“{{param}}” ຈະຕ້ອງບໍ່ຊ້າກວ່າ {{to}}',
        'DateFromTo' => '“{{param}}” ຕ້ອງຢູ່ລະຫວ່າງ {{from}} ~ {{to}}',
        'DateTime' => '“{{param}}” ຕ້ອງສອດຄ່ອງກັບຮູບແບບວັນທີ ແລະເວລາ YYYY-MM-DD HH:mm:ss',
        'DateTimeFrom' => '“{{param}}” ຈະຕ້ອງບໍ່ໄວກວ່າ {{from}}',
        'DateTimeTo' => '“{{param}}” ຕ້ອງໄວກວ່າ {{to}}',
        'DateTimeFromTo' => '“{{param}}” ຕ້ອງຢູ່ລະຫວ່າງ {{from}} ~ {{to}}',

        // ອື່ນໆ
        'Required' => 'ຕ້ອງໃຫ້ “{{param}}”',
    ];

    // “错误提示信息模版”翻译对照表
    protected static $langCode2ErrorTemplates = [
        'en' => self::en,
        'zh' => self::zh,
        'th' => self::th,
        'ph' => self::en,
        'my' => self::en,
        'la' => self::la,
        'lo' => self::la,
        'idn' => self::en,
    ];
}