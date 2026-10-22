//----------------------------------------------------------------------------------
//                           Disciption
//      This is a JavaScript Document for multiple languages webs.
//      Just to build or modify the cookie when language selected. And show
//  the right text in the selected language!
//      This application must be used with multiple languages doucument
//  which in defined type. 

//	Modify At 2006.09.12.13, by yeahone.
//	Modify At 2006.09.14.21, by yeahone.
//	Modify At 2006.09.19.14, by yeahone. for 11 types languages webs.
//----------------------------------------------------------------------------------
//      Define some variables for selecting language. Application select text
//  in right language just by the value of the variable "languageItem"!

//----------------------------------------------------------------------------------
// Language & Language Select Option Content Map Table.

/*
	Description:
		define the language identifier array for the multi-language select box in
	the page "title.asp".
	
	Note:
		firstly, the languageIdSet must map to languageOption one bye one.
		secondly, the content must follow the ui language order in the page
	"title.asp". if the ui changed, the arrays both must be changed at the same time.
*/
//add language support : Thailand(TH) and Indonesia(ID) by Hammond 2011.12.08
var languageIdSet = new Array
(
"en",
"trad_ch",
"simp_ch",
"de",
"es",
"fr",
"pl",
"ru",
"th",
"id"
);

var languageOption = new Array
(
"English",
"Traditional Chinese",
"Simplified Chinese",
"Deutsch",
"Espanol",
"Francais",
"Polski",
"Russian",
"Thailand",
"Indonesia"
);

//----------------------------------------------------------------------------------
//      Set cookie and Set language type which selected.

/*
	Function:
		Set the element "languageId" in the form "from" to the right value,
	which defined in the Array languageIdSet.
	
	Argument:
		input: no.
		output: no.
	Return: no.
	Note:
		the function will be called in the page "title.asp".
*/
function webSetLanguage()
{
	var languageSelectId = document.ipcamForm.languageSet.options[document.ipcamForm.languageSet.selectedIndex].value;
	if(languageSelectId == null)
	{
		languageSelectId = languageIdSet[0];
	}

	document.ipcamForm.languageId.value = languageSelectId;
	setCookieLanguage(languageSelectId);
}

/*
	Function:
		Set cookie and set language which selected in the page "title.asp".
	Argument:
		input: no.
		output: no.
	Return: no.
	Note:
		the page "index.asp" will be reload after setting the cookie.
*/
function setLanguage()
{
	var theSetDate = new Date();
	theSetDate.setDate(theSetDate.getDate() + 365);
	var newValue = document.ipcamForm.languageSet.options[document.ipcamForm.languageSet.selectedIndex].value;
	document.cookie = "IC3015_GLP_Multilanguage=" + newValue + "; expires=" + theSetDate.toGMTString() + "; path=/";
	//document.location.href = "language.asp";
	//location.reload();
}

//----------------------------------------------------------------------------------
//	fetch cookie & fetch language setting in the cookie.

/*
	Function:
		Get cookie field with the name string "c_name".
	Argument:
		input:
			c_name: string object, which must be the cookie field name.
		output: no.
	Return:
		the cookie field value string will be return, when the field name
	found in the cookie. or 0 will be return.
	Note: no.
*/
function getCookie(c_name)
{
	if (document.cookie.length>0)
	{
		c_start = document.cookie.indexOf(c_name + "=")
		if (c_start != -1)
		{
			c_start = c_start + c_name.length + 1;
			c_end = document.cookie.indexOf(";", c_start);
			if (c_end == -1)
				c_end = document.cookie.length;
			return unescape(document.cookie.substring(c_start, c_end));
		}
	}
	return null;
}

/*
	Function:
		Get the item order of the cookie language in the array languageIdset.
	Argument:
		input: no.
		output: no.
	Return:
		the item order of the cookie language in the array languageIdset will
	be return, when the language id is found in the languageIdSet. Or 0 will be
	return.
	Note: no.
*/
function getCookieLanguageItem()
{
	var languageItem1 = 0;
	var cookieLanguageId = getCookie("IC3015_EDIMAX_Multilanguage");
	
	for(var i = 0; i < languageIdSet.length; i++)
	{
		if(cookieLanguageId == languageIdSet[i])
		{
			languageItem1 = i;
			break;
		}
	}
	return languageItem1;
}

//----------------------------------------------------------------------------------
//      Syncing the charset and the language.

/*
	Function:
		Set cookie for the page in "main" frame. for snyc the system
	language setting to the client cookie.
	
	Argument:
		input:
			languageId:	string object. must be some of languageId, which
	defined in the Array languageIdSet.
		output: no.
	Return: no.
	Note:
		the argument "languageId" must be defined in the Array languageIdSet.
		the function cannot be used in the pages which cannot be reload. And
	it should be used in the pages which in "main" frame.
*/
function setCookieLanguage(languageId)
{
	var theSetDate = new Date();
	theSetDate.setDate(theSetDate.getDate() + 365);

	if(languageId != getCookie("IC3015_EDIMAX_Multilanguage"))
	{
		document.cookie = "IC3015_EDIMAX_Multilanguage=" + languageId + "; expires=" + theSetDate.toGMTString() + "; path=/";
		if(languageId == getCookie("IC3015_EDIMAX_Multilanguage"))
		{
			//window.location.reload();
			//parent.window.location.href = "./index.htm";	//reload();	//
			window.top.location.reload();
			//document.form.languageSet.value = languageId;
			/*reload後馬上設好像來不及，再想想看如何讓他選到正確的一項
			document.form.languageSet.selectedIndex=languageId;
			*/
			/*
			parent.topFrame.location.reload();
			parent.lFrame.location.reload();
			parent.main.location.reload();
			*/
		}
	}
}

/*
	Function:
		Set cookie for the index page. for snyc the system language
	setting to the client cookie, while loading the page "index.asp".
	
	Argument:
		input:
			languageId:	string object. must be some of languageIdSet,
		which defined above.	
		output: no.
	Return: no.
	Note:
		the argument "languageId" must be defined in the Array languageIdSet.
		it is just for the page "index.asp".
*/
function setIndexCookie(languageId)
{
	var theSetDate = new Date();
	theSetDate.setDate(theSetDate.getDate() + 365);
	document.cookie = "IC3015_EDIMAX_Multilanguage=" + languageId + "; expires=" + theSetDate.toGMTString() + "; path=/";
}


//----------------------------------------------------------------------------------
//     Show the text in right language type.

/*
	Description:
		get the item order of the cookie language in the array languageIdset.
*/
var languageItem = getCookieLanguageItem();
//var languageItem = 0;

/*
	Function:
		show multi-language string for multi-language web.
	Argument:
		input:
			text: string object. which must be defined in the javascript
		file in array type. there, the files are define as "somefile.var"
		pattern.
		output: no.
	Return:
		it will return the string in  order with "languageItem" in the Array,
	which defined as name with "stringId".
	
	Note:
		it will return null while the stringId is null.
		it will return undefined while the array item with "stringId" name undefined.
*/
function showText(stringId)
{
	if(stringId != null)
		return stringId[languageItem];
	return null;
}

function dw(message)
{
	document.write(showText(message));	
}

//----------------------------------------------------------------------------------
// end of file  --------------------
