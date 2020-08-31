// -- Stuff not necessary for the operation of the program. Mostly easter eggs -- //

// Current date
var currentDate = new Date();

// Yearbook generated date + 5 years
var FiveYearsFromYearbook = new Date(ybdate_js * 1000);
FiveYearsFromYearbook.setFullYear(FiveYearsFromYearbook.getFullYear() + 5);

if(currentDate > FiveYearsFromYearbook){
    main.easteregg("timeago")
}

// Check if user accepts prefers-color-scheme

if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    console.log("El navegador soporte tema oscuro")
    main.theme = true
}
