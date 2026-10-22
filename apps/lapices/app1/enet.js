/**
 * enet.js
 *
 * eNET:   Embedded Network Device Manager
 * Author: Charles Chiou <cchiou@fredatech.com>
 * Copyright (C) 2003-2004, Freda Technologies Corp.
 * All Rights Reserved.
 *
 * This file may be distributed and/or modified under the terms of the
 * GNU General Public License version 2 as published by the Free Software
 * Foundation and appearing in the file COPYING included in the
 * packaging of this file.
 *
 * Licensee holding valid FredaTech eNET license may use this
 * file in accordance with the FredaTech eNET Commercial License Agreement
 * provided with the software.
 *
 * This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING THE
 * WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.
 */

function check_range(val, min, max)
{
	if (val < min || val > max) {
		alert(val + ' is not valid: must be between ' +
		      min + ' and ' + max);
		return false;
	}

	return true;
}

regexp_ipv4 = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;

function check_ip(addr)
{
	var ip;

	ip = addr.match(regexp_ipv4);
	if (ip == null) {
		alert(addr + ' is not a valid ip address');
		return false;
	} else {
		for (var i = 1; i <= 4; i++) {
			if(ip[i] > 255 || ip[i] < 0) {
				alert(addr + ' is not a valid ip address');
				return false;
			}
		}
	}
	return true;
}

function check_mask(addr)
{
	var mask;
	var b = 0;
	
	if (check_ip(addr) == false) {
		return false;
	}
	
	mask = addr.match(regexp_ipv4);
	for (var i = 1; i <= 4; i++) {
		if (mask[i] == 0) {
			b = 1;
		} else if(mask[i] == 255 ||
			  mask[i] == 254 ||
			  mask[i] == 252 ||
			  mask[i] == 248 ||
			  mask[i] == 240 ||
			  mask[i] == 224 ||
			  mask[i] == 192 ||
			  mask[i] == 128) {
			if (b == 1) {
				alert(addr + ' is not a valid netmask');
				return false;
			}
		} else {
			alert(addr + ' is not a valid netmask');
			return false;
		}
	}

	return true;
}

function returnint(bignum)
{
	var goodint, badint;
	var quo, rem;
	badint = bignum;
	goodint = bignum;
	
	if (badint > 2147483647) {
		quo = badint / 2147483648;
		rem = badint % 2147483648;
		
		if ((quo%2) == 1) {
			goodint = -2147483648 + rem;
		} else {
			goodint = 0 + rem;
		}
	} else {
		if (badint < -2147483648) {
			quo = badint / (-2147483648);
			rem = badint % (-2147483648);
			
			if ((quo % 2) == 1) {
				goodint = 2147483649 + rem;
			} else {
				goodint = 0 + rem;
			}
		}
	}

  return goodint;
}

function nwepgen(passphrase, keysize, wepkey1, wepkey2, wepkey3, wepkey4)
{
	var wep_key = new Array(4);
	var i, j;
	var pseed = new Array(4);
	var len;
	var randnumber = 0;
	var tmp;
	var keylen;

	if (keysize.value == 0) 
		keylen = 5;
	else
		keylen = 13;
	
	wep_key[0] = new String('');
	wep_key[1] = new String('');
	wep_key[2] = new String('');
	wep_key[3] = new String('');
	pseed[0] = 0;
	pseed[1] = 0;
	pseed[2] = 0;
	pseed[3] = 0;
	len = passphrase.value.length;
	
	for (i = 0; i < len; i++) {
		pseed[i % 4] = pseed[i%4] ^ passphrase.value.charCodeAt(i);
	}
	
	randnumber =
		(pseed[0])        |
		(pseed[1] <<  8)  |
		(pseed[2] << 16)  |
		(pseed[3] << 24);
	
	for (i = 0; i < 4; i++) {
		for (j = 0; j < keylen; j++) {
			randnumber *= 0x343fd;
			randnumber  = returnint(randnumber);
			randnumber += 0x269ec3;
			randnumber  = returnint(randnumber);
			wep_key[i][j] = ((randnumber >> 16) & 0x7fff);
		}
	}
	
	wepkey1.value = '';
	wepkey2.value = '';
	wepkey3.value = '';
	wepkey4.value = '';
	
	for (j = 0; j < keylen; j++) {
		tmp = (wep_key[0][j] & 0x00F0) / 16;
		wepkey1.value = wepkey1.value + tmp.toString(16);
		tmp = wep_key[0][j] % 16;
		wepkey1.value = wepkey1.value + tmp.toString(16);
		tmp = (wep_key[1][j] & 0x00F0) / 16;
		wepkey2.value = wepkey2.value + tmp.toString(16);
		tmp = wep_key[1][j] % 16;
		wepkey2.value = wepkey2.value + tmp.toString(16);
		tmp = (wep_key[2][j] & 0x00F0) / 16;
		wepkey3.value = wepkey3.value + tmp.toString(16);
		tmp = wep_key[2][j] % 16;
		wepkey3.value = wepkey3.value + tmp.toString(16);
		tmp = (wep_key[3][j] & 0x00F0) / 16;
		wepkey4.value =	wepkey4.value + tmp.toString(16);
		tmp = wep_key[3][j] % 16;
		wepkey4.value =	wepkey4.value + tmp.toString(16);
	}
	
}

regexp_wepkey10hex = /^([\d,a-f,A-F]{10})$/;
regexp_wepkey26hex = /^([\d,a-f,A-F]{26})$/;

function is_valid_wepkey(wepkey, keylen)
{
	 if (keylen == 5)
		 return regexp_wepkey10hex.test(wepkey);
	 else
		 return regexp_wepkey26hex.test(wepkey);
}

function validate_wep(keylen, wepkey1, wepkey2, wepkey3, wepkey4)
{
	if (wepkey1.value == '' || 
	    is_valid_wepkey(wepkey1.value, keylen) == false) {
		alert('WEP Key 1 is invalid!');
		return false;
	}
	if (wepkey2.value == '' || 
	    is_valid_wepkey(wepkey2.value, keylen) == false) {
		alert('WEP Key 2 is invalid!');
		return false;
	}
	if (wepkey3.value == '' || 
	    is_valid_wepkey(wepkey3.value, keylen) == false) {
		alert('WEP Key 3 is invalid!');
		return false;
	}
	if (wepkey4.value == '' || 
	    is_valid_wepkey(wepkey4.value, keylen) == false) {
		alert('WEP Key 4 is invalid!');
		return false;
	}

	return true;
}

