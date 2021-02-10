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
 * @version   1.0.2
 */

//Шаблоны для "Уголок слёз и поцелуев" (Kiss and Cry)

/* ################################################################################################
Информационная панель:
    Кнопка:Segment; - Название соревнования, группа и сегмент
*/
FS_EventName = `
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

/* ################################################################################################
Информационная панель:
    Кнопка: 1nd Score; - Показать индивидуальные результаты проката "Уголок слёз и поцелуев"
*/
FS_KissAndCry = `
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
// Количество линий участников
LineCountWeb = 7;
// Табло "Уголок слёз и поцелуев"
ConfigKissAndCry = true;