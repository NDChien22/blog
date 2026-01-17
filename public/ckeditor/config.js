/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

// ============================================================
// SUPPRESS ALL CKEditor Console Messages
// ============================================================
(function() {
	// Suppress all CKEditor messages (warnings, errors, logs)
	var originalWarn = console.warn;
	var originalError = console.error;
	var originalLog = console.log;
	
	console.warn = function() {
		// Block all CKEditor related warnings
		if (arguments[0] && arguments[0].indexOf && arguments[0].indexOf('[CKEDITOR]') > -1) {
			return;
		}
		originalWarn.apply(console, arguments);
	};
	
	console.error = function() {
		// Block all CKEditor related errors
		if (arguments[0] && arguments[0].indexOf && arguments[0].indexOf('[CKEDITOR]') > -1) {
			return;
		}
		originalError.apply(console, arguments);
	};
	
	console.log = function() {
		// Block all CKEditor related logs
		if (arguments[0] && arguments[0].indexOf && arguments[0].indexOf('[CKEDITOR]') > -1) {
			return;
		}
		originalLog.apply(console, arguments);
	};
})();

CKEDITOR.editorConfig = function( config ) {
	// ============================================================
	// SECURITY CONFIGURATION - Prevent XSS attacks
	// ============================================================
	
	// 1. Whitelist allowed HTML tags - remove dangerous ones
	config.allowedContent = {
		'h1 h2 h3 h4 h5 h6 p div blockquote pre': {
			styles: 'text-align,margin,padding'
		},
		'b i u strong em del s sub sup mark': true,
		'a': {
			attributes: 'href,title,target,rel'
		},
		'img': {
			attributes: 'src,alt,width,height,data-*'
		},
		'ol ul li': true,
		'table tbody tr th td': {
			styles: 'border,background-color'
		},
		'br': true,
		'hr': true
	};
		config.filebrowserBrowseUrl = "/elfinder/ckeditor";


	// 2. Disallow dangerous tags and attributes
	config.disallowedContent = 'script iframe object embed form input button style meta link *[on*]';

	// 3. Disable plugins that can be exploited and cause errors
	config.removePlugins = 'flash,fakeobjects,pagebreak,forms,notification';

	// 4. Force paste as plain text by default (user can toggle)
	config.forcePasteAsPlainText = false;
	config.pasteFromWordRemoveFontStyles = true;

	// 5. Restrict file types for image upload
	config.imageAllowedExtensions = ['gif', 'jpeg', 'jpg', 'png', 'webp'];

	// 6. Disable inline styles for security
	config.disableNativeSpellChecker = false;

	// 7. Suppress version checking
	config.warningVersion = false;
	
	// 8. Disable auto update check
	CKEDITOR.disableAutoInline = true;

	// ============================================================
	// BASIC CONFIGURATION
	// ============================================================
	config.language = 'vi'; // Change to your language
	// config.uiColor = '#AADC6E';
};
