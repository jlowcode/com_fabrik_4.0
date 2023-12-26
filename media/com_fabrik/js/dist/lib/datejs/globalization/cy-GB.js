/*! Fabrik */

Date.CultureInfo={name:"cy-GB",englishName:"Welsh (United Kingdom)",nativeName:"Cymraeg (y Deyrnas Unedig)",dayNames:["Dydd Sul","Dydd Llun","Dydd Mawrth","Dydd Mercher","Dydd Iau","Dydd Gwener","Dydd Sadwrn"],abbreviatedDayNames:["Sul","Llun","Maw","Mer","Iau","Gwe","Sad"],shortestDayNames:["Sul","Llun","Maw","Mer","Iau","Gwe","Sad"],firstLetterDayNames:["S","L","M","M","I","G","S"],monthNames:["Ionawr","Chwefror","Mawrth","Ebrill","Mai","Mehefin","Gorffennaf","Awst","Medi","Hydref","Tachwedd","Rhagfyr"],abbreviatedMonthNames:["Ion","Chwe","Maw","Ebr","Mai","Meh","Gor","Aws","Med","Hyd","Tach","Rhag"],amDesignator:"a.m.",pmDesignator:"p.m.",firstDayOfWeek:1,twoDigitYearMax:2029,dateElementOrder:"dmy",formatPatterns:{shortDate:"dd/MM/yyyy",longDate:"dd MMMM yyyy",shortTime:"HH:mm:ss",longTime:"HH:mm:ss",fullDateTime:"dd MMMM yyyy HH:mm:ss",sortableDateTime:"yyyy-MM-ddTHH:mm:ss",universalSortableDateTime:"yyyy-MM-dd HH:mm:ssZ",rfc1123:"ddd, dd MMM yyyy HH:mm:ss GMT",monthDay:"MMMM dd",yearMonth:"MMMM yyyy"},regexPatterns:{jan:/^ion(awr)?/i,feb:/^chwe(fror)?/i,mar:/^maw(rth)?/i,apr:/^ebr(ill)?/i,may:/^mai/i,jun:/^meh(efin)?/i,jul:/^gor(ffennaf)?/i,aug:/^aws(t)?/i,sep:/^med(i)?/i,oct:/^hyd(ref)?/i,nov:/^tach(wedd)?/i,dec:/^rhag(fyr)?/i,sun:/^dydd sul/i,mon:/^dydd llun/i,tue:/^dydd mawrth/i,wed:/^dydd mercher/i,thu:/^dydd iau/i,fri:/^dydd gwener/i,sat:/^dydd sadwrn/i,future:/^next/i,past:/^last|past|prev(ious)?/i,add:/^(\+|aft(er)?|from|hence)/i,subtract:/^(\-|bef(ore)?|ago)/i,yesterday:/^yes(terday)?/i,today:/^t(od(ay)?)?/i,tomorrow:/^tom(orrow)?/i,now:/^n(ow)?/i,millisecond:/^ms|milli(second)?s?/i,second:/^sec(ond)?s?/i,minute:/^mn|min(ute)?s?/i,hour:/^h(our)?s?/i,week:/^w(eek)?s?/i,month:/^m(onth)?s?/i,day:/^d(ay)?s?/i,year:/^y(ear)?s?/i,shortMeridian:/^(a|p)/i,longMeridian:/^(a\.?m?\.?|p\.?m?\.?)/i,timezone:/^((e(s|d)t|c(s|d)t|m(s|d)t|p(s|d)t)|((gmt)?\s*(\+|\-)\s*\d\d\d\d?)|gmt|utc)/i,ordinalSuffix:/^\s*(st|nd|rd|th)/i,timeContext:/^\s*(\:|a(?!u|p)|p)/i},timezones:[{name:"UTC",offset:"-000"},{name:"GMT",offset:"-000"},{name:"EST",offset:"-0500"},{name:"EDT",offset:"-0400"},{name:"CST",offset:"-0600"},{name:"CDT",offset:"-0500"},{name:"MST",offset:"-0700"},{name:"MDT",offset:"-0600"},{name:"PST",offset:"-0800"},{name:"PDT",offset:"-0700"}]};