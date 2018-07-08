<div id="app">

    <div class="wrap">
        <h2 class='opt-title'>
            <span id='icon-options-general' class='analytics-options'>
                <img src="<?php echo AFX_URL . '/assets/images/icon16.png'; ?>" alt="">
            </span>
            AppointFox -
            <?php echo __( 'Settings', 'appointfox' ); ?>
        </h2>
        <div class="appointfox-tbs">
            <div class="container-fluid">
                <div class="appointfox-wrap">
                    <div class="row">

                        <template v-if="isLoading">
                            <div class="loading-bar">
                                <i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i>
                                <br>
                                <?php _e( 'Loading', 'appointfox' ); ?>.....
                            </div>
                        </template>

                        <template v-else>
                            <div>

                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#general" aria-controls="general" role="tab" data-toggle="tab">
                                            <?php _e( 'General', 'appointfox' ); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#payment" aria-controls="payment" role="tab" data-toggle="tab">
                                            <?php _e( 'Payment', 'appointfox' ); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#emailtemplate" aria-controls="emailtemplate" role="tab" data-toggle="tab">
                                            <?php _e( 'Email Templates', 'appointfox' ); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#clientpage" aria-controls="clientpage" role="tab" data-toggle="tab">
                                            <?php _e( 'Client\'s Page', 'appointfox' ); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#shortcode" aria-controls="shortcode" role="tab" data-toggle="tab">
                                            <?php _e( 'Shortcode', 'appointfox' ); ?>
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="general">

                                        <form class="form-horizontal">
                                            <div class="form-group">
                                                <label for="FormSettingsBusinessName" class="col-sm-2 control-label">
                                                    <?php _e( 'Business Name', 'appointfox' ); ?>
                                                </label>
                                                <div class="col-sm-8">
                                                    <input v-model="formSettings.business_name" type="text" class="form-control" id="FormSettingsBusinessName" placeholder="<?php _e( 'Enter your business name', 'appointfox' ); ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="FormSettingsInstructions" class="col-sm-2 control-label">
                                                    <?php _e( 'Instructions', 'appointfox' ); ?>
                                                </label>
                                                <div class="col-sm-8">
                                                    <textarea rows="4" v-model="formSettings.instructions" class="form-control" id="FormSettingsInstructions" placeholder="<?php _e( 'Write any instructions here to show to clients (optional)', 'appointfox' ); ?>"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="FormSettingsWeekStart" class="col-sm-2 control-label">
                                                    <?php _e( 'Week Start On', 'appointfox' ); ?>
                                                </label>
                                                <div class="col-sm-4">
                                                    <select v-model="formSettings.week_start_on" class="form-control" id="FormSettingsWeekStart">
                                                        <option value="Sunday">
                                                            <?php _e( 'Sunday', 'appointfox' ); ?>
                                                        </option>
                                                        <option value="Monday">
                                                            <?php _e( 'Monday', 'appointfox' ); ?>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="FormSettingsTimeFormat" class="col-sm-2 control-label">
                                                    <?php _e( 'Time Format', 'appointfox' ); ?>
                                                </label>
                                                <div class="col-sm-4">
                                                    <select v-model="formSettings.time_format" class="form-control" id="FormSettingsTimeFormat">
                                                        <option value="AM/PM">AM/PM (Ex. 2.00pm)</option>
                                                        <option value="24 hour">24 hour (Ex. 14:00)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-8">
                                                    <button type="button" class="btn btn-success ladda-button ladda-button-general" data-style="expand-left" @click="saveSettings('general')">
                                                        <span class="ladda-label">
                                                            <?php _e( 'Save Changes', 'appointfox' ); ?>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>

                                        </form>

                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="payment">

                                        <form class="form-horizontal">
                                            <div class="form-group">
                                                <label for="FormSettingsCurrency" class="col-sm-2 control-label">
                                                    <?php _e( 'Currency', 'appointfox' ); ?>
                                                </label>
                                                <div class="col-sm-4">
                                                    <select id="FormSettingsCurrency" v-model="formSettings.currency" class="form-control el-select2">
                                                        <option label="$ (USD)" value="USD">$ (USD)</option>
                                                        <option label="€ (EUR)" value="EUR">€ (EUR)</option>
                                                        <option label="£ (GBP)" value="GBP">£ (GBP)</option>
                                                        <option label="£ EGP (EGP)" value="EGP">£ EGP (EGP)</option>
                                                        <option label="$ AUD (AUD)" value="AUD">$ AUD (AUD)</option>
                                                        <option label="$ NZD (NZD)" value="NZD">$ NZD (NZD)</option>
                                                        <option label="$ CAD (CAD)" value="CAD">$ CAD (CAD)</option>
                                                        <option label="CHF (CHF)" value="CHF">CHF (CHF)</option>
                                                        <option label="R$ (BRL)" value="BRL">R$ (BRL)</option>
                                                        <option label="$ HKD (HKD)" value="HKD">$ HKD (HKD)</option>
                                                        <option label="$ SGD (SGD)" value="SGD">$ SGD (SGD)</option>
                                                        <option label="$ MXN (MXN)" value="MXN">$ MXN (MXN)</option>
                                                        <option label="₪ (ILS)" value="ILS">₪ (ILS)</option>
                                                        <option label="¥ (JPY)" value="JPY">¥ (JPY)</option>
                                                        <option label="NT$ (TWD)" value="TWD">NT$ (TWD)</option>
                                                        <option label="Kr (NOK)" value="NOK">Kr (NOK)</option>
                                                        <option label="DKK (DKK)" value="DKK">DKK (DKK)</option>
                                                        <option label="SEK (SEK)" value="SEK">SEK (SEK)</option>
                                                        <option label="MYR (MYR)" value="MYR">MYR (MYR)</option>
                                                        <option label="BHD (BHD)" value="BHD">BHD (BHD)</option>
                                                        <option label="₹ (INR)" value="INR">₹ (INR)</option>
                                                        <option label="R (ZAR)" value="ZAR">R (ZAR)</option>
                                                        <option label="QR (QAR)" value="QAR">QR (QAR)</option>
                                                        <option label="SR (SAR)" value="SAR">SR (SAR)</option>
                                                        <option label="AED (AED)" value="AED">AED (AED)</option>
                                                        <option label="₦ (NGN)" value="NGN">₦ (NGN)</option>
                                                        <option label="RUB ₽  (RUB)" value="RUB">RUB ₽ (RUB)</option>
                                                        <option label="TL (TRY)" value="TRY">TL (TRY)</option>
                                                        <option label="KSh (KES)" value="KES">KSh (KES)</option>
                                                        <option label="Br (ETB)" value="ETB">Br (ETB)</option>
                                                        <option label="lei (RON)" value="RON">lei (RON)</option>
                                                        <option label="฿ (THB)" value="THB">฿ (THB)</option>
                                                        <option label="лв (BGN)" value="BGN">лв (BGN)</option>
                                                        <option label="TT$ (TTD)" value="TTD">TT$ (TTD)</option>
                                                        <option label="₱ (PHP)" value="PHP">₱ (PHP)</option>
                                                        <option label="Ft (HUF)" value="HUF">Ft (HUF)</option>
                                                        <option label="zł (PLN)" value="PLN">zł (PLN)</option>
                                                        <option label="Rs (LKR)" value="LKR">Rs (LKR)</option>
                                                        <option label="₩ (KRW)" value="KRW">₩ (KRW)</option>
                                                        <option label="K.D. (KWD)" value="KWD">K.D. (KWD)</option>
                                                        <option label="Íkr (ISK)" value="ISK">Íkr (ISK)</option>
                                                        <option label="MAD (MAD)" value="MAD">MAD (MAD)</option>
                                                        <option label="$ COP (COP)" value="COP">$ COP (COP)</option>
                                                        <option label="₴ (UAH)" value="UAH">₴ (UAH)</option>
                                                        <option label="ر.ع (OMR)" value="OMR">ر.ع (OMR)</option>
                                                        <option label="Rp (IDR)" value="IDR">Rp (IDR)</option>
                                                        <option label="₫ (VND)" value="VND">₫ (VND)</option>
                                                        <option label="Kč (CZK)" value="CZK">Kč (CZK)</option>
                                                        <option label=" ¥ (CNY)" value="CNY"> ¥ (CNY)</option>
                                                        <option label="$ BBD (BBD)" value="BBD">$ BBD (BBD)</option>
                                                        <option label="$ CLP (CLP)" value="CLP">$ CLP (CLP)</option>
                                                        <option label="USh (UGX)" value="UGX">USh (UGX)</option>
                                                        <option label="₡ (CRC)" value="CRC">₡ (CRC)</option>
                                                        <option label="TSh (TZS)" value="TZS">TSh (TZS)</option>
                                                        <option label="₨ (MUR)" value="MUR">₨ (MUR)</option>
                                                        <option label="$ JMD (JMD)" value="JMD">$ JMD (JMD)</option>
                                                        <option label="FRw (RWF)" value="RWF">FRw (RWF)</option>
                                                        <option label="Q (GTQ)" value="GTQ">Q (GTQ)</option>
                                                        <option label="ƒ (ANG)" value="ANG">ƒ (ANG)</option>
                                                        <option label="CFA (XOF)" value="XOF">CFA (XOF)</option>
                                                        <option label="FCFA (XAF)" value="XAF">FCFA (XAF)</option>
                                                        <option label="RSD (RSD)" value="RSD">RSD (RSD)</option>
                                                        <option label="S/ (PEN)" value="PEN">S/ (PEN)</option>
                                                        <option label="EC$ (XCD)" value="XCD">EC$ (XCD)</option>
                                                        <option label="JOD (JOD)" value="JOD">JOD (JOD)</option>
                                                        <option label="RD$ (DOP)" value="DOP">RD$ (DOP)</option>
                                                        <option label="kn (HRK)" value="HRK">kn (HRK)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="FormSettingsPaymentMethod" class="col-sm-2 control-label">
                                                    <?php _e( 'Accept Payment', 'appointfox' ); ?>
                                                </label>
                                                <div class="col-sm-4">
                                                    <select v-model="formSettings.payment_method" class="form-control" id="FormSettingsPaymentMethod">
                                                        <option value="None">
                                                            <?php _e( 'None', 'appointfox' ); ?>
                                                        </option>
                                                        <option value="PayPal">PayPal</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div v-if="formSettings.payment_method == 'PayPal'">
                                                <div class="form-group">
                                                    <!-- <label for="FormSettingsPaypalSandbox" class="col-sm-2 control-label">PayPal Sandbox</label>
													<div class="col-sm-4">
														<input v-model="formSettings.is_paypal_sandbox" type="checkbox" id="FormSettingsPaypalSandbox">
													</div> -->
                                                    <label for="" class="col-sm-2 control-label">PayPal
                                                        <?php _e( 'Sandbox', 'appointfox' ); ?>
                                                    </label>
                                                    <div class="col-sm-4">
                                                        <div class="can-toggle can-toggle--size-small">
                                                            <input v-model="formSettings.is_paypal_sandbox" id="FormSettingsPaypalSandbox" type="checkbox">
                                                            <label for="FormSettingsPaypalSandbox">
                                                                <div class="can-toggle__switch" data-checked="On" data-unchecked="Off"></div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-offset-2 col-sm-8">
                                                        <h4>
                                                            <?php _e( 'Live', 'appointfox' ); ?>
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="FormSettingsPaypalProdClientId" class="col-sm-2 control-label">Client ID</label>
                                                    <div class="col-sm-8">
                                                        <input v-model="formSettings.paypal_prod_clientid" type="text" class="form-control" id="FormSettingsPaypalProdClientId" placeholder="<?php _e( 'Enter your PayPal production clientID', 'appointfox' ); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="FormSettingsPaypalProdSecret" class="col-sm-2 control-label">Secret</label>
                                                    <div class="col-sm-8">
                                                        <input v-model="formSettings.paypal_prod_secret" type="text" class="form-control" id="FormSettingsPaypalProdSecret" placeholder="<?php _e( 'Enter your PayPal production secret', 'appointfox' ); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-offset-2 col-sm-8">
                                                        <h4>
                                                            <?php _e( 'Sandbox', 'appointfox' ); ?>
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="FormSettingsPaypalSandboxClientId" class="col-sm-2 control-label">Client ID</label>
                                                    <div class="col-sm-8">
                                                        <input v-model="formSettings.paypal_sandbox_clientid" type="text" class="form-control" id="FormSettingsPaypalSandboxClientId"
                                                            placeholder="<?php _e( 'Enter your PayPal sandbox clientID', 'appointfox' ); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="FormSettingsPaypalSandboxSecret" class="col-sm-2 control-label">Secret</label>
                                                    <div class="col-sm-8">
                                                        <input v-model="formSettings.paypal_sandbox_secret" type="text" class="form-control" id="FormSettingsPaypalSandboxSecret"
                                                            placeholder="<?php _e( 'Enter your PayPal sandbox secret', 'appointfox' ); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-8">
                                                    <button type="button" class="btn btn-success ladda-button ladda-button-payment" data-style="expand-left" @click="saveSettings('payment')">
                                                        <span class="ladda-label">
                                                            <?php _e( 'Save Changes', 'appointfox' ); ?>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="clientpage">
                                        <form class="form-horizontal">
                                            <div class="form-group">
                                                <label for="FormSettingsBackgroundColor" class="col-sm-2 control-label">
                                                    <?php _e( 'Background Color', 'appointfox' ); ?>
                                                </label>
                                                <div class="col-sm-2">
                                                    <div class="appointfox-color-picker-wrapper">
                                                        <input v-model="formSettings.background_color" id="FormSettingsBackgroundColor" type="text" class="form-control color-field-background-color"
                                                            placeholder="" style="display: inline-block">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="FormSettingsFontColor" class="col-sm-2 control-label">
                                                    <?php _e( 'Font Color', 'appointfox' ); ?>
                                                </label>
                                                <div class="col-sm-2">
                                                    <div class="appointfox-color-picker-wrapper">
                                                        <input v-model="formSettings.font_color" id="FormSettingsFontColor" type="text" class="form-control color-field-font-color"
                                                            placeholder="" style="display: inline-block">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="FormSettingsFontSize" class="col-sm-2 control-label">
                                                    <?php _e( 'Font Size', 'appointfox' ); ?>
                                                </label>
                                                <div class="col-sm-2">
                                                    <input v-model="formSettings.font_size" id="FormSettingsFontSize" type="text" class="form-control" placeholder="" style="display: inline-block">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <button type="button" class="btn btn-success ladda-button ladda-button-clientpage" data-style="expand-left" @click="saveSettings('clientpage')">
                                                        <span class="ladda-label">
                                                            <?php _e( 'Save Changes', 'appointfox' ); ?>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="emailtemplate">
                                        <form class="form-horizontal">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="FormSettingsNotificationName" class="col-sm-2 control-label">
                                                            <?php _e( 'Select Template', 'appointfox' ); ?>
                                                        </label>
                                                        <div class="col-sm-10">
                                                            <select id="FormSettingsNotificationName" v-model="formNotification.id" @change="loadNotification()" class="form-control">
                                                                <option value="">
                                                                    <?php _e( 'Choose template', 'appointfox' ); ?>...</option>
                                                                <option v-for="notification in notifications" :value="notification.id">{{ notification.name }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div id="FormNotification" v-show="isShowFormNotification">
                                                        <div v-if="formNotification.id == 3">
                                                            <div class="form-group">
                                                                <p class="well">
                                                                    <?php _e( 'To send scheduled notifications please execute the following command hourly with your cron', 'appointfox' ); ?>:
                                                                    <br>
                                                                    <br> * 1 * * * wget -q -O -
                                                                    <?php echo get_site_url(); ?>/wp-cron.php
                                                                </p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="FormSettingsReminderHours" class="col-sm-2 control-label">
                                                                    <?php _e( 'Send This Reminder', 'appointfox' ); ?>
                                                                </label>
                                                                <div class="col-sm-6">
                                                                    <div class="input-group">
                                                                        <input v-model="formSettings.reminder_hours" class="form-control" id="FormSettingsReminderHours">
                                                                        <span class="input-group-addon" id="basic-addon2">
                                                                            <?php _e( 'hour(s) before their appointment', 'appointfox' ); ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="hidden" id="FormSettingsNotificationType" value="" v-model="formNotification.type">
                                                            <input type="hidden" id="FormSettingsNotificationId" value="" v-model="formNotification.id">
                                                            <label for="FormSettingsNotificationSubject" class="col-sm-2 control-label">
                                                                <?php _e( 'Subject', 'appointfox' ); ?>
                                                            </label>
                                                            <div class="col-sm-10">
                                                                <input v-model="formNotification.subject" class="form-control" id="FormSettingsNotificationSubject" placeholder="<?php _e( 'Enter email subject', 'appointfox' ); ?>"></input>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="FormSettingsNotificationMessage" class="col-sm-2 control-label">
                                                                <?php _e( 'Message', 'appointfox' ); ?>
                                                            </label>
                                                            <div class="col-sm-10">
                                                                <?php
																wp_editor(
																	'', 'FormSettingsNotificationMessage', array(
																		'tinymce' => true,
																		'quicktags' => true,
																		'media_buttons' => false,
																		'textarea_rows' => 20,
																		'editor_height' => 300,
																	)
																);
																?>
                                                                    <!-- <textarea rows="20" id="FormSettingsNotificationMessage"></textarea> -->
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-offset-2 col-sm-10">
                                                                <button type="button" class="btn btn-success ladda-button ladda-button-email" data-style="expand-left" @click="saveSettings('email')">
                                                                    <span class="ladda-label">
                                                                        <?php _e( 'Save Changes', 'appointfox' ); ?>
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="well" v-show="isShowFormNotification">
                                                        <p>
                                                            <?php _e( 'Some tokens you can use', 'appointfox' ); ?>:
                                                            <br>
                                                            <br>
                                                            <b>%title%</b> —
                                                            <?php _e( 'To display the event\'s title', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%name%</b> —
                                                            <?php _e( 'To display the person\'s name', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%email%</b> —
                                                            <?php _e( 'To display the person\'s email address', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%service%</b> —
                                                            <?php _e( 'To display the selected service', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%calendar%</b> —
                                                            <?php _e( 'To display the calendar name (if one is used) for this appointment', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%datetime%</b> —
                                                            <?php _e( 'To display the appointment date and time', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%business_name%</b> —
                                                            <?php _e( 'To display the business\'s name', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%payment_method%</b> —
                                                            <?php _e( 'To display the payment\'s method', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%payment_amount%</b> —
                                                            <?php _e( 'To display the payment\'s amount', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%payment_status%</b> —
                                                            <?php _e( 'To display the payment\'s status', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%payment_id%</b> —
                                                            <?php _e( 'To display the payment\'s id', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%price%</b> —
                                                            <?php _e( 'To display the appointment\'s price', 'appointfox' ); ?>.
                                                            <br>
                                                            <b>%url%</b> —
                                                            <?php _e( 'To display link to the appointment details page', 'appointfox' ); ?>.
                                                            <br>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>
                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="shortcode">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4>
                                                    <?php _e( 'Display the Appointment Booking Form', 'appointfox' ); ?>
                                                </h4>
                                                <p>
                                                    <?php _e( 'You can use this shortcode to display the front-end appointment booking form', 'appointfox' ); ?>.
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <input id="shortcode-appointfox-appointment" value="[appointfox-appointment]" type="text" class="form-control" readonly="readonly">
                                                    <span class="input-group-btn">
                                                        <button data-clipboard-target="#shortcode-appointfox-appointment" class="btn btn-default btn-copy" type="button">
                                                            <?php _e( 'Copy', 'appointfox' ); ?>
                                                        </button>
                                                    </span>
                                                </div>
                                                <!-- /input-group -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>