var app = new Vue({
  el: "#app",
  data: {
    isLoading: false,
    isShowFormNotification: false,
    formSettings: {
      business_name: "",
      instructions: "",
      week_start_on: "Sunday",
      time_format: "AM/PM",
      currency: "USD",
      reminder_hours: 24,
      payment_method: "None",
      paypal_prod_clientid: "",
      paypal_prod_secret: "",
      paypal_sandbox_clientid: "",
      paypal_sandbox_secret: "",
      is_paypal_sandbox: 1,
      background_color: "#fff",
      font_color: "#000",
      font_size: "16px"
    },
    templates: [
      "Initial Confirmation",
      "Reminder",
      "Cancellation",
      "Reshedule",
      "Payment Paid"
    ],
    notifications: [],
    formNotification: {
      id: "",
      type: "",
      name: "",
      is_active: true,
      is_copy: false,
      subject: "",
      message: ""
    }
  },
  mounted: function() {},
  created: function() {
    var self = this;
    self.isLoading = true;

    axios
      .get(
        afx_vars.ajax_url +
          "?action=afx-settings-get&_ajax_nonce=" +
          afx_vars.get_settings_nonce
      )
      .then(function(response) {
        self.formSettings = response.data.settings;
        self.notifications = response.data.notifications;
        self.isLoading = false;

        Vue.nextTick(function() {
          jQuery(".el-select2").select2({
            width: "100%",
            theme: "bootstrap"
          });

          jQuery(".color-field-background-color").wpColorPicker({
            change: function(event, ui) {
              var theColor = ui.color.toString();
              app.formSettings.background_color = theColor;
            },
            width: 200
          });

          jQuery(".color-field-font-color").wpColorPicker({
            change: function(event, ui) {
              var theColor = ui.color.toString();
              app.formSettings.font_color = theColor;
            },
            width: 200
          });
        });
      })
      .catch(function(error) {
        console.log(error);
      });
  },
  methods: {
    loadNotification: function() {
      var self = this;

      if (self.formNotification.id == "") {
        self.isShowFormNotification = false;
        return;
      }

      Vue.nextTick(function() {
        self.isShowFormNotification = true;
        jQuery("#FormSettingsNotificationMessage-tmce").click();
      });

      var msg = "";
      Vue.nextTick(function() {
        for (var i = 0; i < self.notifications.length; i++) {
          if (self.notifications[i].id == self.formNotification.id) {
            self.formNotification.id = self.notifications[i].id;
            self.formNotification.name = self.notifications[i].name;
            self.formNotification.type = self.notifications[i].type;
            self.formNotification.is_copy = self.notifications[i].is_copy;
            self.formNotification.is_active = self.notifications[i].is_active;
            self.formNotification.subject = self.notifications[i].subject;
            msg = self.notifications[i].message;

            if (tinyMCE.activeEditor == null) {
              jQuery("#FormSettingsNotificationMessage").html(
                self.notifications[i].message
              );
            } else {
              setTimeout(function() {
                tinymce.activeEditor.setContent(msg);
                tinymce.activeEditor.focus();
              }, 100);
            }
          }
        }
      });
    },
    saveSettings: function(button) {
      var l = Ladda.create(document.querySelector(".ladda-button-" + button));
      l.start();

      var self = this;

      if (self.formNotification.id) {
        self.formNotification.message = tinymce.activeEditor.getContent();

        for (var i = 0; i < self.notifications.length; i++) {
          if (self.notifications[i].id == self.formNotification.id) {
            self.notifications[i].subject = self.formNotification.subject;
            self.notifications[i].message = tinymce.activeEditor.getContent();
          }
        }
      }

      // Init currency setting
      self.formSettings.currency = jQuery("#FormSettingsCurrency").val();

      axios
        .post(
          afx_vars.ajax_url +
            "?action=afx-settings-save&_ajax_nonce=" +
            afx_vars.save_settings_nonce,
          {
            settings: self.formSettings,
            notification: self.formNotification
          }
        )
        .then(function(response) {
          if (response.data.success) {
            toastr.success(afx_vars.labels.settings_saved);
          }

          l.stop();
          l.remove();
        })
        .catch(function(error) {
          console.log(error);
        });
    }
  }
});

(function($) {
  $(document).ready(function() {
    // Ladda.bind('.ladda-button');

    jQuery(".el-select2").select2();

    var clip = new Clipboard(".btn-copy");

    clip.on("success", function(e) {
      toastr.success(afx_vars.labels.copied+"!");
    });

    jQuery("#FormSettingsNotificationMessage-tmce").click();
  });
})(jQuery);
