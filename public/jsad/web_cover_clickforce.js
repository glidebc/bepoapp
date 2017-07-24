var $insCF = jQuery('ins[class=clickforceads]');

function callback(status) {
    console.log("clickforce status = " + status);
    if (status == '20')
        $insCF.hide();
}
