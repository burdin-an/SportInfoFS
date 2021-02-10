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
 */

 //Шаблоны для "Уголок слёз и поцелуев" (Kiss and Cry)

//Информация об участнике
var FS_UserInfo = ``;

//Показать индивидуальные результаты проката
var FS_UserResult = ``;

//Информация об официальном лице
var FS_JudgeOne = ``;

//Стартовый лист - Полный
//Стартовый лист - По группам
//Промежуточные результаты соревнования
//Информация о всех судьях
var FS_UsersList = ``;

//Название программы выступления
var FS_EventName = `
<div id="boardSegment" class="cl_boardEvent">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'></div>
            <div id='CategoryName'></div>
            <hr />
            <div id='SegmentName'></div>
        </div>
    </div>
</div>`;

//Показать индивидуальные результаты проката "Уголок слёз и поцелуев"
var FS_KissAndCry = `
<div id="boardKissAndCry" class="cl_board0">
    <div class='TitleBG'>
        <div id='TitleBlock'>
            <div id='EventName'></div>
            <div id='CategoryName'></div>
            <hr />
            <div id='SegmentName'></div>
        </div>
    </div>
    <div id="LineName"><div class="LineType01" id="pName"></div></div>
    <div id="LineFirst">
        <div>
            <div class="LineType03">Техника:<div id='pTES'></div></div>
            <div class="LineType03">Компоненты:<div id='pTCS'></div></div>
            <div class="LineType03">Снижения: <div id='pDedSum'></div></div>
            <div class="LineType03">Бонусы: <div id='pBonus'></div></div>
            <div class="LineType03">Баллы за выступление:<div id='pSeqPoints'></div></div>
        </div>
    </div>
    <div id="LineElementOne">
        <div id="LineSecond">
            <div>Элементы:</div>
            <div>
                <div class="LineTechDetail">Элементы:</div>
                <div class="LineTechDetail">Info:</div>
                <div class="LineTechDetail">Base:</div>
                <div class="LineTechDetail">GoE:</div>
                <div class="LineTechDetail">Баллы:</div>
            </div>
        </div>
        <div id="LineThree">
            <div class="LineType03">Итоговая оценка:<div id='pTPoint'></div></div>
            <div class="LineType03">Текущее итоговое место:<div id='pTRank'></div></div>
        </div>
        <div id="LineFourth">
            <div>Нарушения:</div>
            <div>
                <div class="LineTechDetail">Нарушения:</div>
                <div class="LineTechDetail">Баллы:</div>
            </div>
        </div>
    </div>
</div>`;
//Таймер
var FS_Timer = ``;
// Количество линий участников
var LineCountWeb = 7;
// Показать время 
var ConfigShowTimer = false;
// Табло "Уголок слёз и поцелуев"
var ConfigKissAndCry = true;
// Отладочная информация
var debuging = false;
// 
var TitleSubNameJudgeAll = "Официальные лица:";
// 
var TitleSubName3nd = "Промежуточные результаты:";
// 
var TitleSubNameWup = "Разминка, группа №:";
// 
var TitleSubNameStartList = "Стартовый лист:";

//Официальные лица
var OfficialFunction = [];
//Судья №1...99
OfficialFunction['JDG'] = "Судья №";
//Помощник технического специалиста
OfficialFunction['STS'] = "Ассистент технического специалиста";
//Технический специалист
OfficialFunction['TSP'] = "Технический специалист";
//Технический контролёр
OfficialFunction['TCO'] = "Технический контролёр";
// Старший судья
OfficialFunction['ERF'] = "Старший судья";
//Оператор ввода данных или видео оператор
OfficialFunction['DOP'] = "Оператор ввода данных";
//Пока незнаю кто это
OfficialFunction['REP'] = "Representative";
//Пока незнаю кто это
OfficialFunction['TDG'] = "Технический делегат";