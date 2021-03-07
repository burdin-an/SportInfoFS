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
LineCountWebParticipant = 14;
// Автоматически переключать списки участников в 
KissAndCryAutoScrollParticipantList = true;
// Через сколько секунд переключать списки участников
AutoCaruselBoardTime = 10;
// Табло "Уголок слёз и поцелуев"
ConfigKissAndCry = true;
//Разрешить разворачивать во весь экран
AllowFullScreen = true;

/* ################################################################################################
Информационная панель:
    Кнопка:Segment; - Название соревнования, группа и сегмент
*/
const FS_EventName = `
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
const FS_KissAndCry = (data) => `
<div id="boardKissAndCry" class="cl_board0">
<div class="container-fluid">
    <div class="row">
        <div class="col-8">
            <div class='TitleBG'>
                <div id='TitleBlock'>
                    <div id='CategoryName'>${data['Category']} / ${data['Segment']}</div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="LineName" id="pName">${data['FullName']}</div>
                    </div>
                </div>
                <div class="row LineFirst">
                    <div class="col center">
                        <div class="LineType03">Техника:<div id='pTES'>${data['TechnicPoint']}</div></div>
                    </div>
                    <div class="col center">
                        <div class="LineType03">Компоненты:<div id='pTCS'>${data['ComponentPoint']}</div></div>
                    </div>
                    <div class="col center">
                        <div class="LineType03">Снижения: <div id='pDedSum'>${data['DeductionPoint']}</div></div>
                    </div>
                    <div class="col center">
                        <div class="LineType03">Бонусы: <div id='pBonus'>${data['BonusPoint']}</div></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col center">
                        <div class="LineSeqPoint">Баллы за выступление:<div id='pSeqPoints'>${data['Points']}</div></div>
                    </div>
                    <div class="col">
                        <div class="LineTotalPoint">Сумма баллов:<div id='pTPoint'>${data['TPoints']}</div></div>
                    </div>
                    <div class="col">
                        <div class="LineTotalRank">Текущее место:<div id='pTRank'>${data['TRank']}</div></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div id="LineElement" class="container-fluid">
                            <div class="row">
                                <div class="col-4 LineTechDetail">Элементы:</div>
                                <div class="col LineTechDetail">Info:</div>
                                <div class="col LineTechDetail">Base:</div>
                                <div class="col LineTechDetail">GoE:</div>
                                <div class="col LineTechDetail">Баллы:</div>
                            </div>
                            ${data['ElementLine']}
                        </div>
                    </div>
                    <div class="col-6">
                        <div id="LineDeduction" class="container-fluid">
                            <div class="row">
                                <div class="col LineTechDetail">Нарушения:</div>
                                <div class="col-3 LineTechDetail">Кол-во:</div>
                            </div>
                            ${data['DeductionLine']}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div id="participantListContainer">${data['PlaceLine']}</div>
        </div>
    </div>
</div>
</div>`;
/* Шаблон: Первый блок обёртки
   ${data['IDContainer']} - 
 */
const FS_KissAndCryLineWrapperFirst = (data) => `<div data-index="${data['IDContainer']}" class="participantListContainerItem container-fluid active">`;

/* Шаблон: Последующие блоки обёртки
   ${data['IDContainer']} - 
 */
const FS_KissAndCryLineWrapperSecond = (data) => `</div><div data-index="${data['IDContainer']}" class="participantListContainerItem container-fluid">`;

/* Шаблон: Списка участников с оценками:

   ${data["CurrentClass"]} - 
   ${data["Sort"]}         - 
   ${data["FullName"]}     - 
   ${data["City"]}         - 
   ${data["Point"]}        - 
 */
const FS_KissAndCryLineParticipant = (data) => `<div class='row LineRow ${data["CurrentClass"]}'><div class="col-1 LineSort">${data["Sort"]}</div><div class="col LineText">${data["FullName"]}</div><div class='col-2 LineLast'>${data["Point"]}</div></div>`;
/* Шаблон: Списка участников с оценками:

   ${data['ElementName']}  - 
   ${data['ElementInfo']}  - 
   ${data['ElementBV']}    - 
   ${data['ElementGOE']}   - 
   ${data['ElementPoint']} - 
 */
const FS_KissAndCryLineElement = (data) => `<div class="row"><div class="col-4 LineTechDetail">${data['ElementName']}</div><div class="col LineTechDetail">${data['ElementInfo']}</div><div class="col LineTechDetail">${data['ElementBV']}</div><div class="col LineTechDetail">${data['ElementGOE']}</div><div class="col LineTechDetail">${data['ElementPoint']}</div></div>`;
/* Шаблон: Списка участников с оценками:

   ${data['DeductionName']}  - 
   ${data['DeductionCount']} - 
 */
const FS_KissAndCryLineDeduction = (data) => `<div class="row"><div class="col LineTechDetail">${data['DeductionName']}</div><div class="col-3 LineTechDetail">${data['DeductionCount']}</div></div>`;

/* Шаблон: Кнопки далее */
const FS_KissAndCryNextButton = `<div class="container-fluid"><div class="row"><div class="col"><a href='#' id="ButtonNextParticipant" class='btn btn-info w-100' role="button">Далее</a></div></div></div>`