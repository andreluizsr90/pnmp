   
const moneyMaskOptions = {
    mask: 'R$ num', // Display "R$" symbol before the number
    blocks: {
        num: {
            // Use the Number mask type
            mask: Number,
            thousandsSeparator: '.',
            radix: ',', // Decimal separator for Brazilian Real
            scale: 2, // Number of decimal places
            padFractionalZeros: true, // Pad with zeros if the user types less than 2 decimal places
            signed: false, // No negative values for currency
        }
    },
    // Optional: Use lazy placeholder to guide the user
    lazy: false,
    placeholderChar: '0'
};


(function(){
    $(".select2").select2();

    $(".postal_code").each(function() {
        IMask(this, { mask: '00000-000', lazy: false });
    });
    
    $(".cnpj").each(function() {
        IMask(this, { mask: '00.000.000/0000-00', lazy: false });
    });
    
    $(".cpf").each(function() {
        IMask(this, { mask: '000.000.000-00', lazy: false });
    });

    $(".money").each(function() {
        IMask(this, moneyMaskOptions);
    });

    $(".dataTableSimple").DataTable();
})();