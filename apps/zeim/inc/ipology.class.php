<?php
/**
* ipology
* Gathers some information about given IP from the RIPE DB
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*
*
*     __     __)               _____                      ___
*    (, /|  /  ,              (, /     /)           ,    (,   )      /)    /)
*      / | /    ___   _         /  ___(/   _   __            / _   _(/  _ (/
*   ) /  |/  _(_// (_(_(_   ___/__(_) / )_(_(_/ (__(_      _/_(_(_(_(__(/_/ )_
*  (_/   '                /   /                         )   /
*                        (__ /                         (__ /
* ****************************************************************************
* @author    Nima Johari Zadeh <nijoza91-at-gmail-dot-com>
* @package   ipology
* @file      ipology.class.php
* @copyright 2007 Nima Johari Zadeh
*
* *******************************************************
        IMPORTANT NOTICE
        PLEASE READ license FILE!
* *******************************************************
*/

class ipology
{
    var $_ip = "255.255.255.255";
    var $_whois = "whois.ripe.net";
    var $_port = 43;
    var $_timeout = 10;
    var $_buffer = "";
    var $out = array();
    var $array_return_mode;
    var $_total = 0;

    public function __construct(...$arguments) { $this->ipology(...$arguments); }

function ipology($ip_array, $array_return_mode = "array")
    {
        $this->array_return_mode = $array_return_mode;
        $ip_array = (array) $ip_array;
        $this->_total = count($ip_array);
        $i = 0;
        foreach($ip_array as $ip)
        {
            if($this->isValidIP($ip))
            {
                $this->_ip = $ip;
                if($this->_fetch())
                {
                    $this->out[$ip] = $this->_extract();
                    if($this->out[$ip]["inetnum"] == "0.0.0.0 - 255.255.255.255")
                        $this->out[$ip] = 102;
                }
                else
                {
                    $this->out[$ip] = 101; //could not connect
                }
            }
            else
                $this->out[$ip] = 100; //incorrect IP format
        }
    }

    function _fetch()
    {
        if(!$sock = @fsockopen($this->_whois, $this->_port, $errno, $errstr, 10))
            return false;
        else
        {
            fputs($sock, "{$this->_ip}\n");
            while(!feof($sock))
                $buffer .= fgets($sock, 10240);
            fclose($sock);
            $this->_buffer = $buffer;
            return true;
        }
    }

    function _extract()
    {
        $ret = array();
        $w = array("inetnum","org","netname","descr","country","admin-c","tech-c","status","mnt-by","mnt-lower","mnt-routes","source","organisation","org-name","org-type","address","phone","fax-no","e-mail","mnt-ref","person","nic-hdl","route","origin");
        foreach($w as $get)
        {
            if(preg_match_all("/$get:\s*(.*+)/i", $this->_buffer, $matches))
            {
                if(count($matches[1]) > 1)
                {
                    if($this->array_return_mode == "array")
                        $ret[$get] = $matches[1];
                    else
                        foreach($matches[1] as $ras)
                            $ret[$get] .= $ras . "\n";
                }
                else
                    $ret[$get] = $matches[1][0];
            }
        }
        return $ret;
    }

    function isValidIp($ip)
    {
        return preg_match("/(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/", $ip) ? true : false;
    }

    function out($w=0)
    {
        return $this->out;
    }
}
?>
