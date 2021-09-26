function submit() {

    // Total
    let total_value = total;
    let discount_value = discount;
    let advance_value = advance;
    var balance_value = balance;


    alert(balance_value.value);
    let abc = [total_value, discount_value, advance_value, balance_value];

    make_data()

    // clear_all(abc);
}

function make_data() {
    var data = new URLSearchParams();

    // Product List
    data.append("Lines", no);

    let id = no--;
    for (no; no > 0; no--) {

        let p_id = "product_" + id;
        let p_value = p_id.value;
        //data.append("s" + p_id, p_value);
        alert(p_id + " : " + p_value)
    }

    //  var url = "echo.php?" + data.toString();
    // location.href = url;
}

/*
function clear_all(abc) {
    alert("dis" + abc[1]);
    alert("row" + no);
    abc.forEach(element => {
        element.value = "";
    });


}
*/