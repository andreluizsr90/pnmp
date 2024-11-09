class PnmpPhoneField {
    constructor(placeId, fieldName, varName) {
      this.placeId = placeId;
      this.fieldName = fieldName;
      this.varName = varName;
    }

    template (varName, fieldName, value) {

        return `
        <li class="list-group-item">
            <i class="ti ti-square-x fs-4 me-2 text-danger" onclick="${varName}.removePhone(this)"></i>
            ${value}
            <input type="hidden" name="${fieldName}[]" value="${value}" />
        </li>`;
    }

    removePhone(obj) {
        $(obj).parent().remove();
    }

    addPhone () {

        let data = this.template(this.varName, this.fieldName, this.field.val());
        
        this.field.val("");

        this.listPhones.append(data);
    }

    loadData(dataToLoad = []) {
        dataToLoad.forEach(element => {
          this.listPhones.append(this.template(this.varName, this.fieldName, element));
        });
    }

    static init(placeId, fieldName, varName) {
      
      let cls = new PnmpPhoneField(placeId, fieldName, varName);
      let placeElement = $("#" + cls.placeId);

      placeElement.append(`
      <div class="row place-field-add">
          <div class="col-9">
              <input name="${fieldName}-add" type="text" class="form-control" />
          </div>
          <div class="col-3">
              <button type="button" class="btn btn-rounded btn-sm btn-outline-dark mt-1">
                  <i class="ti ti-plus"></i>
              </button>
          </div>
      </div>
      <div class="row place-lists-phone" style="margin-left: 5px">
        <ul class="list-group list-group-flush">
        </ul>
      </div>`);

      cls.buttonAdd = $("#" + placeId + " button");
      cls.listPhones = $("#" + placeId + " .list-group");
      cls.field = $("#" + placeId + " input[name="+fieldName+"-add");

      IMask(document.getElementsByName(`${fieldName}-add`)[0], {
        mask: [
          { mask: '(00) 0000-0000', length: 10, lazy: true },   // 10 characters format
          { mask: '(00) 00000-0000', length: 11, lazy: true }  // 11 characters format
        ],
        dispatch: function(appended, dynamicMasked) {
          const value = (dynamicMasked.value + appended).replace(/\D/g, ''); // Remove any non-digit
          return value.length <= 10 
          ? dynamicMasked.compiledMasks[0] 
          : dynamicMasked.compiledMasks[1];
        }
      });

      cls.buttonAdd.click(function() {
        cls.addPhone();
      });

      return cls;
    }
}