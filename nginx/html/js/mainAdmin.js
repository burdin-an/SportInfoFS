
/**
 * Проект "Информатор спортивных соревнований: фигурное катание на коньках"
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    Бурдин А.Н. <support@it-sakh.net>
 * @copyright Бурдин А.Н. <support@it-sakh.net>
 * @link      http://www.it-sakh.info/SportInfo/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   1.0.1
 */

// Отладочная информация
var debuging = true;
$(document).ready(function(){
    let ws;
    function connect() {
        ws = new WebSocket('ws://' + window.location.hostname + ':8000');
        ws.onopen = function() {
            if (debuging != false) {console.log('WebSocket connected');};

        };
        ws.onclose = function(e) {
            EventDB = [];
            if (debuging != false) {console.log('Socket is closed. Reconnect will be attempted in 1 second.', e.reason);};
            setTimeout(function() {
                connect();
            }, 1000);
        };

        ws.onerror = function(err) {
            if (debuging != false) {console.error('Socket encountered error: ', err.message, 'Closing socket');};
            ws.close();
        };
    }
    

    connect();
    $('#Name').click(function() {
        ws.send("Name");
    });
    $('#Segment').click(function() {
        ws.send("Segment");
    });
    $('#Clear').click(function(s) {
        ws.send("Clear");
    });
    $('#ClearTablo').click(function(s) {
        ws.send("ClearTablo");
    });
    $('#ClearTV').click(function(s) {
        ws.send("ClearTV");
    });
    $('#ClearKissAndCry').click(function(s) {
        ws.send("ClearKissAndCry");
    });
    $('#ResultPersonal').click(function() {
        ws.send("ResultPersonal");
    });
    $('#ResultAll').click(function() {
        ws.send("ResultAll");
    });
    $('#StartList').click(function() {
        ws.send("StartList");
    });
    $('#WarmGroup').click(function() {
        ws.send("WarmGroup");
    });
    $('#JudgeAll').click(function() {
        ws.send("JudgeAll");
    });
    $('#VoiceOneMinute').click(function() {
        ws.send("VoiceOneMinute");
    });
    $('#VoiceWarmCompleted').click(function() {
        ws.send("VoiceWarmCompleted");
    });
    $('#VoiceStartGame').click(function() {
        ws.send("VoiceStartGame");
    });
    $('#ReloadKissAndCry').click(function() {
        ws.send("ReloadKissAndCry");
    });
    $('#ReloadTablo').click(function() {
        ws.send("ReloadTablo");
    });
    $('#ReloadTV').click(function() {
        ws.send("ReloadTV");
    });
    $('#XXXXXXX').click(function() {
        ws.send("XXXXXXX");
    });
});
