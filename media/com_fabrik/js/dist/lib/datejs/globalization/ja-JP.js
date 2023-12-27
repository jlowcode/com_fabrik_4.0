/*! Fabrik */

Date.CultureInfo={name:"ja-JP",englishName:"Japanese (Japan)",nativeName:"日本語 (日本)",dayNames:["日曜日","月曜日","火曜日","水曜日","木曜日","金曜日","土曜日"],abbreviatedDayNames:["日","月","火","水","木","金","土"],shortestDayNames:["日","月","火","水","木","金","土"],firstLetterDayNames:["日","月","火","水","木","金","土"],monthNames:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"],abbreviatedMonthNames:["1","2","3","4","5","6","7","8","9","10","11","12"],amDesignator:"午前",pmDesignator:"午後",firstDayOfWeek:0,twoDigitYearMax:2029,dateElementOrder:"ymd",formatPatterns:{shortDate:"yyyy/MM/dd",longDate:"yyyy'年'M'月'd'日'",shortTime:"H:mm",longTime:"H:mm:ss",fullDateTime:"yyyy'年'M'月'd'日' H:mm:ss",sortableDateTime:"yyyy-MM-ddTHH:mm:ss",universalSortableDateTime:"yyyy-MM-dd HH:mm:ssZ",rfc1123:"ddd, dd MMM yyyy HH:mm:ss GMT",monthDay:"M'月'd'日'",yearMonth:"yyyy'年'M'月'"},regexPatterns:{jan:/^1(月)?/i,feb:/^2(月)?/i,mar:/^3(月)?/i,apr:/^4(月)?/i,may:/^5(月)?/i,jun:/^6(月)?/i,jul:/^7(月)?/i,aug:/^8(月)?/i,sep:/^9(月)?/i,oct:/^10(月)?/i,nov:/^11(月)?/i,dec:/^12(月)?/i,sun:/^日曜日/i,mon:/^月曜日/i,tue:/^火曜日/i,wed:/^水曜日/i,thu:/^木曜日/i,fri:/^金曜日/i,sat:/^土曜日/i,future:/^next/i,past:/^last|past|prev(ious)?/i,add:/^(\+|aft(er)?|from|hence)/i,subtract:/^(\-|bef(ore)?|ago)/i,yesterday:/^yes(terday)?/i,today:/^t(od(ay)?)?/i,tomorrow:/^tom(orrow)?/i,now:/^n(ow)?/i,millisecond:/^ms|milli(second)?s?/i,second:/^sec(ond)?s?/i,minute:/^mn|min(ute)?s?/i,hour:/^h(our)?s?/i,week:/^w(eek)?s?/i,month:/^m(onth)?s?/i,day:/^d(ay)?s?/i,year:/^y(ear)?s?/i,shortMeridian:/^(a|p)/i,longMeridian:/^(a\.?m?\.?|p\.?m?\.?)/i,timezone:/^((e(s|d)t|c(s|d)t|m(s|d)t|p(s|d)t)|((gmt)?\s*(\+|\-)\s*\d\d\d\d?)|gmt|utc)/i,ordinalSuffix:/^\s*(st|nd|rd|th)/i,timeContext:/^\s*(\:|a(?!u|p)|p)/i},timezones:[{name:"UTC",offset:"-000"},{name:"GMT",offset:"-000"},{name:"EST",offset:"-0500"},{name:"EDT",offset:"-0400"},{name:"CST",offset:"-0600"},{name:"CDT",offset:"-0500"},{name:"MST",offset:"-0700"},{name:"MDT",offset:"-0600"},{name:"PST",offset:"-0800"},{name:"PDT",offset:"-0700"}]};