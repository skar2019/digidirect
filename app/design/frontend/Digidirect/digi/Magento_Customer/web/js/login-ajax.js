console.log("Login AJAX Test!");

const loadconfigdata = localStorage.getItem("configData-60007039-e927-ec11-aaf7-061f6a8be99c");
const myObj = JSON.parse(loadconfigdata);
var magento_pa_id = myObj.c;

localStorage.setItem("Magento_PA_Id", magento_pa_id); 
alert("magento_pa_id: " + magento_pa_id);

var param = '{pa_id=' + magento_pa_id + '}';
var YOUR_URL_HERE = 'getpaidsalesforce';

jQuery.ajax({
    url: YOUR_URL_HERE,
    type: "POST",
    data: {pa_id: param},
    dataType: "json",
    success: function() {
        alert("Thank you for subscribing!");
    },
    error: function() {
        alert("There was an error. Try again please!");
    }
});