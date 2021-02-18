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
 * @version   1.0.3
 */

//Шаблоны для "Уголок слёз и поцелуев" (Kiss and Cry)

// Количество линий участников
LineCountWeb = 7;
// Табло "Уголок слёз и поцелуев"
ConfigKissAndCry = true;
//Разрешить разворачивать во весь экран
AllowFullScreen = true;

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
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="LineName" id="pName"></div>
            </div>
        </div>
        <div class="row LineFirst">
            <div class="col center">
                <div class="LineType03">Техника:<div id='pTES'></div></div>
            </div>
            <div class="col center">
                <div class="LineType03">Компоненты:<div id='pTCS'></div></div>
            </div>
            <div class="col center">
                <div class="LineType03">Снижения: <div id='pDedSum'></div></div>
            </div>
            <div class="col center">
                <div class="LineType03">Бонусы: <div id='pBonus'></div></div>
            </div>
            <div class="col center">
                <div class="LineType03">Баллы за выступление:<div id='pSeqPoints'></div></div>
            </div>
        </div>
        <div class="row">
            <div class="col-5">
                <div id="LineElement" class="container-fluid">
                    <div class="row">
                        <div class="col-4 LineTechDetail">Элементы:</div>
                        <div class="col LineTechDetail">Info:</div>
                        <div class="col LineTechDetail">Base:</div>
                        <div class="col LineTechDetail">GoE:</div>
                        <div class="col LineTechDetail">Баллы:</div>
                    </div>
                </div>
            </div>
            <div class="col-5">
                <div id="LineDeduction" class="container-fluid">
                    <div class="row">
                        <div class="col LineTechDetail">Нарушения:</div>
                        <div class="col-3 LineTechDetail">Кол-во:</div>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div id="LineResult" class="container-fluid">
                    <div class="row">
                        <div class="LineTotalPoint">Сумма баллов:<div id='pTPoint'></div></div>
                    </div>
                    <div class="row">
                        <div class="LineTotalRank">Текущее место:<div id='pTRank'></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>`;