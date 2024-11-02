class PnmpSelectFieldApi {
    constructor(url, placeId, fieldName, filter, required, selected) {
      this.url = url;
      this.placeId = placeId;
      this.fieldName = fieldName;
      this.filter = filter;
      this.required = required;
      this.selected = selected;
    }
    
    template(units) { 
      const options = units.map((value) =>
          `<option value='${value._id}' ${this.selected == value._id ? 'selected' : ''} >${value.name}</option>`
      );

      return `
        <div class="row my-2" id="${this.fieldName}-container">
          <div class="col">
            <select ${this.required ? 'required' : ''} name="${this.fieldName}" id="${this.fieldName}-field" class="${this.fieldName}-cls form-control">
              <option value=''>-- ${this.required ? 'Selecione' : 'Nenhum'} --</option>
              ${options.join("")}
            </select>
          </div>
          <div class="col-2">
            <div id="${this.fieldName}-loading" class="spinner-border text-dark fs-1" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      `;
    }

    static init(url, placeId, fieldName, filter = {}, required = true, selected = false) {
      
      let cls = new PnmpSelectFieldApi(url, placeId, fieldName, filter, required, selected);
      let placeElement = $("#" + cls.placeId);
      let loadingIcon = `#${cls.fieldName}-loading`;
      let queryString = Object.getOwnPropertyNames(cls.filter).length > 0 ? "?" + new URLSearchParams(cls.filter).toString() : "";

      placeElement.html("");
      $.ajax({ 
        type: 'GET', 
        url: cls.url + queryString,
        dataType: 'json',
        beforeSend: function() {
          $(loadingIcon).removeClass("d-none");
        },
        success: function (json) { 
          if(json.success) {
            placeElement.append(cls.template(json.data));
          }
        }
      }).always(function(){
        $(loadingIcon).addClass("d-none");
        $(`#${cls.fieldName}-field`).select2();
      });

      return cls;
    }
}