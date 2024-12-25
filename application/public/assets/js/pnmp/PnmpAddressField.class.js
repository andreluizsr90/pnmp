class PnmpAddressField {
    constructor(placeId, onerror) {
      this.placeId = placeId;
      this.buttonSearch = $("#" + placeId + " button");
      this.buttonSearchIcon = $("#" + placeId + " button .ti-map-search");
      this.buttonSearchIconLoading = $("#" + placeId + " button .spinner-border");
      this.fieldCep = $("#" + placeId + " input[name=postal_code]");
      this.fieldLine1 = $("#" + placeId + " input[name=line_1]");
      this.fieldLine2 = $("#" + placeId + " input[name=line_2]");
      this.fieldCity = $("#" + placeId + " input[name=city]");
      this.onerror = onerror;
    }

    search() {
        let cepValue = this.fieldCep.val().replace(/\D/g,'');
        let url = "https://viacep.com.br/ws/"+ cepValue +"/json/";
        let cls = this;

        $.ajax({ 
            type: 'GET', 
            url: url,
            dataType: 'json',
            beforeSend: function() {
                cls.buttonSearch.attr('disabled', true);
                cls.buttonSearchIcon.css('display', 'none');
                cls.buttonSearchIconLoading.css('display', '');
            },
            success: function (json) { 
                if(json.erro == 'true') {
                    Swal.fire({
                        icon: "error",
                        text: "CEP inválido.",
                    });
                } else {
                    cls.fieldLine1.val(json.logradouro);
                    cls.fieldLine2.val(json.bairro);
                    cls.fieldCity.val(json.localidade);
                }
            },
            error: function (request, status, error) {
                if(typeof cls.onerror === 'function') {
                    cls.onerror();
                } else {
                    Swal.fire({
                        icon: "error",
                        text: "CEP inválido.",
                    });
                }
            }
          }).always(function(){
            cls.buttonSearch.attr('disabled', false);
            cls.buttonSearchIcon.css('display', '');
            cls.buttonSearchIconLoading.css('display', 'none');
          });
    }

    static init(placeId, onerror) {
      
      let cls = new PnmpAddressField(placeId, onerror);

      cls.buttonSearch.click(function() {
        cls.search();
      });
      
      cls.buttonSearchIconLoading.css('display', 'none');

      return cls;
    }
}