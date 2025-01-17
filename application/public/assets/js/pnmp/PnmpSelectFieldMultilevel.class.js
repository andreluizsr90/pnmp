class PnmpSelectFieldMultilevel {
    constructor(urlParent, url, variableInstance, placeId, fieldName, required = false, callback = null) {
      this.urlParent = urlParent;
      this.url = url;
      this.variableInstance = variableInstance;
      this.placeId = placeId;
      this.fieldName = fieldName;
      this.nextLevel = 0;
      this.required = required;
      this.callback = callback;
      this.alwaysExecute = null;
    }
    
    template(level, data, selected) { 
      const options = data.map((value) =>
          `<option value='${value._id}' ${value._id == selected ? 'selected' : ''} data-haschildren='${value.has_children}'>${value.name}</option>`
      );

      return `
        <div class="row my-2" id="${this.fieldName}-${level}">
          <div class="col">
            <select ${this.required ? 'required' : ''} data-level="${level}" name="${this.fieldName}[]" id="${this.fieldName}-${level}-field" class="form-control" onchange="${this.variableInstance}.fieldSelected(this)">
              <option value=''>-- Selecione --</option>
              ${options.join("")}
            </select>
          </div>
          <div class="col-2">
            <div id="${this.fieldName}-${level}-loading" class="spinner-border text-dark fs-1 d-none" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      `;
    }
    
    fieldSelected (obj) {
      let selectedField = $(obj).find("option:selected");
      let level = parseInt($(obj).attr('data-level'));
      if(selectedField.attr('data-haschildren') === 'true') {
        this.selectLevel(level, selectedField.val());
      } else {
        this.eraseLevels(level);
        if(this.callback != null) {
          this.callback(selectedField.val());
        }
      }
    }
    
    eraseLevels (level) {
      let placeAdmUnit = $("#" + this.placeId);
      for (let index = (level + 1); index <= this.nextLevel; index++) {
        placeAdmUnit.find(`#${this.fieldName}-${index}`).remove();
      }
      this.nextLevel = level;
    }
    
    selectLevel (level, id = null, selected = null) {
      this.eraseLevels(level);

      let cls = this;
      let placeAdmUnit = $("#" + cls.placeId);
      let prefixFieldId = `#${cls.fieldName}-${level}`;
      cls.nextLevel++;
      let prefixNextFieldId = `#${cls.fieldName}-${cls.nextLevel}`;

      $.ajax({ 
        type: 'GET', 
        url: cls.urlParent + (id != null ? '?id=' + id : ''),
        dataType: 'json',
        beforeSend: function() {
          $(prefixFieldId + "-loading").removeClass("d-none");
        },
        success: function (json) { 
          if(json.success) {
            placeAdmUnit.append(cls.template(cls.nextLevel, json.data, selected));
          }
        }
      }).always(function(){

        if(cls.alwaysExecute != null) {
          cls.alwaysExecute();
        }

        $(prefixFieldId + "-loading").addClass("d-none");
        $(prefixNextFieldId + "-field").select2();
      });
      
    }

    init(selected = '') {
      $("#" + this.placeId).html("");
      if(selected.length == 0) {
        this.selectLevel(0);
      } else {
        this.loadFieldSelected(selected);
      }
    }

    loadFieldSelected(selected) {
      let cls = this;

      $.ajax({ 
        type: 'GET', 
        url: cls.url + '?id=' + selected,
        dataType: 'json',
        success: function (json) { 
          if(json.success) {
            cls.selectLevelsLoaded(json.data.reverse(), 0);
          }
        }
      });
    }

    selectLevelsLoaded (parents, level) {
      let cls = this;
      let placeAdmUnit = $("#" + cls.placeId);
      let prefixFieldId = `#${cls.fieldName}-${level}`;
      cls.nextLevel++;
      let prefixNextFieldId = `#${cls.fieldName}-${cls.nextLevel}`;

      $.ajax({ 
        type: 'GET', 
        url: cls.urlParent + (parents[0].parent_id != null ? '?id=' + parents[0].parent_id : ''),
        dataType: 'json',
        beforeSend: function() {
          $(prefixFieldId + "-loading").removeClass("d-none");
        },
        success: function (json) { 
          if(json.success) {
            placeAdmUnit.append(cls.template(cls.nextLevel, json.data, parents[0]._id));
            parents.shift();
            if(parents.length > 0){
              cls.selectLevelsLoaded(parents, cls.nextLevel);
            } else {
              if(cls.callback != null) {
                cls.callback($(prefixNextFieldId + "-field").val());
              }
            }
          }
        }
      }).always(function(){

        if(cls.alwaysExecute != null) {
          cls.alwaysExecute();
        }
        
        $(prefixFieldId + "-loading").addClass("d-none");
        $(prefixNextFieldId + "-field").select2();
      });
      
    }
}