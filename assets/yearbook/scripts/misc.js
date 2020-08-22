// -- Stuff not necessary for the operation of the program. Mostly easter eggs -- //

// Current date + 5 years
var currentDate = new Date();

// Yearbook generated date + 5 years
var FiveYearsFromYearbook = new Date(ybdate_js * 1000);
FiveYearsFromYearbook.setFullYear(FiveYearsFromYearbook.getFullYear() + 5);

if(currentDate > FiveYearsFromYearbook){
    document.getElementById("recap").innerHTML = lang["misc"]["longtime"]

    // Support translations
    document.getElementById("languages").addEventListener("click", () => {
        document.getElementById("recap").innerHTML = lang["misc"]["longtime"]
    })
}