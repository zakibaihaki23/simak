<template>
  <div class="regist">
    <h1 class="mb-10">Input Data Kegiatan Harian Camat</h1>
    <v-row></v-row>
    <v-form v-model="isFormValid">
      <v-row>
        <div class="row">
          <div class="col mx-auto">
            <div class="form-group row">
              <div>
                <p>
                  Periode Laporan
                  <span style="color: red">*</span>
                </p>
                <v-menu
                  ref="menu"
                  v-model="menu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      :value="date_picker"
                      prepend-inner-icon="mdi-calendar"
                      single-line
                      clearable
                      outlined
                      label="Periode"
                      class="form"
                      v-bind="attrs"
                      v-on="on"
                      :rules="[rules.required]"
                      @click:clear="date = ''"
                      @input="dateDisabled"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    @change="dateDisabled"
                    locale="id"
                    v-model="date"
                    type="month"
                    no-title
                    scrollable
                    @input="$refs.menu.save(date)"
                  >
                  </v-date-picker>
                </v-menu>
              </div>
            </div>
            <div class="form-group-row">
              <div class="col-lg-6">
                <v-checkbox
                  v-model="nihil"
                  true-value="null"
                  false-value="0"
                  :label="`Tidak ada laporan (NIHIL)`"
                  :disabled="
                    waktu != '' || time != '' || form.tempat != null || form.uraian != null
                  "
                >
                </v-checkbox>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-lg-6">
                <b-skeleton-wrapper :loading="loading">
                  <template #loading>
                    <b-skeleton></b-skeleton>
                  </template>
                  <p>Tanggal Kegiatan</p>
                  <v-menu
                    ref="menu2"
                    v-model="menu2"
                    :close-on-content-click="false"
                    :return-value.sync="waktu"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        :value="tanggal_kegiatan"
                        prepend-inner-icon="mdi-calendar"
                        single-line
                        clearable
                        outlined
                        class="form"
                        v-bind="attrs"
                        label="Tanggal Kegiatan"
                        v-on="on"
                        @input="dateSelected"
                        :disabled="disabledDate || nihil == 'null'"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      @change="dateSelected"
                      locale="id"
                      v-model="waktu"
                      no-title
                      scrollable
                      :min="date"
                      :max="bulan"
                      @input="$refs.menu2.save(waktu)"
                      :show-current="date"
                    >
                    </v-date-picker>
                  </v-menu>
                </b-skeleton-wrapper>
              </div>
              <div class="col-lg-6">
                <p>Waktu Kegiatan</p>
                <v-menu
                  ref="menu3"
                  v-model="menu3"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  :return-value.sync="time"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="290px"
                  :disabled="timeDisabled"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="time"
                      prepend-inner-icon="mdi-clock"
                      single-line
                      readonly
                      clearable
                      outlined
                      label="Waktu Kegiatan"
                      class="form"
                      v-bind="attrs"
                      v-on="on"
                      :disabled="timeDisabled"
                    ></v-text-field>
                  </template>
                  <v-time-picker
                    format="24hr"
                    v-if="menu3"
                    v-model="time"
                    full-width
                    @click:minute="$refs.menu3.save(time)"
                  ></v-time-picker>
                </v-menu>
              </div>
            </div>
            <div class="form-group row">
              <div>
                <p>Tempat Kegiatan</p>
                <v-text-field
                  outlined
                  class="form"
                  label="Tempat Kegiatan"
                  v-model="form.tempat"
                  clearable
                  single-line
                  :disabled="nihil == 'null'"
                ></v-text-field>
              </div>
            </div>
            <div class="form-group row">
              <div>
                <p>Uraian Kegiatan</p>
                <v-textarea
                  label="Uraian Kegiatan"
                  clearable
                  clear-icon="mdi-close-circle"
                  single-line
                  outlined
                  class="form"
                  v-model="form.uraian"
                  :disabled="nihil == 'null'"
                ></v-textarea>
              </div>
            </div>
          </div>
        </div>
      </v-row>

      <br />
      <br />
      <br />

      <v-divider></v-divider>
      <div>
        <b-row>
          <b-col class="text-right">
            <v-btn
              :to="{ path: '/kegiatan-camat' }"
              color="#4FC3F7"
              class="button"
              outlined
              style="
                box-sizing: content-box;
                border-radius: 25px;
                width: 100px;
                height: 45px;
                padding: 4px;
              "
              >Batalkan</v-btn
            >&nbsp;
            <v-btn
              v-if="nihil == 'null'"
              style="
                background: #007bff;
                color: white;
                box-sizing: content-box;
                border-radius: 25px;
                width: 100px;
                height: 45px;
                padding: 4px;
              "
              class="save"
              @click="save()"
              :disabled="!isFormValid"
              >Simpan</v-btn
            >
            <v-btn
              v-else
              style="
                background: #007bff;
                color: white;
                box-sizing: content-box;
                border-radius: 25px;
                width: 100px;
                height: 45px;
                padding: 4px;
              "
              class="save"
              @click="save()"
              :disabled="
                tanggal_kegiatan == null ||
                time == null ||
                form.tempat == null ||
                form.uraian == null ||
                !isFormValid
              "
              >Simpan</v-btn
            >
          </b-col>
          <v-dialog v-model="dialogOverlay" max-width="300" persistent>
            <div>
              <v-card color="primary" dark class="text-center">
                <v-card-text>
                  Mohon tunggu sebentar......
                  <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
                </v-card-text>
              </v-card>
            </div>
          </v-dialog>
          <v-overlay :value="overlay">
            <v-progress-circular indeterminate color="blue"></v-progress-circular>
          </v-overlay>
        </b-row>
      </div>
    </v-form>
  </div>
</template>

<script>
  import { mapGetters } from "vuex";

  export default {
    data() {
      return {
        overlay: false,
        kosong: "",
        confirmDialog: false,
        dialogOverlay: false,
        date: "",
        waktu: "",
        time: "",
        nihil: null,
        menu: false,
        menu2: false,
        menu3: false,
        timeDisabled: true,
        search: null,
        saveDisabled: true,
        isFormValid: false,
        disabledDate: true,
        loading: true,

        rules: {
          required: (value) => !!value || "Required",
          counter: (value) => value.length <= 12 || "Max 30 Characters",
        },
        form: {
          tempat: null,
          uraian: null,
        },
        value: String,
        val_confirm: String,
        warehouse: "",
        warehouse_id: "",
        kelurahan_id: "",
        kelurahan: [],
        kecamatan: [],
        error: {},
        placeholder: " ",
        readonly: false,
        disabled: false,
        outlined: true,
        clearable: true,
      };
    },

    watch: {
      search: {
        handler: function (val) {
          this.renderData(val);
        },
        deep: true,
      },
      overlay(val) {
        val &&
          setTimeout(() => {
            this.overlay = false;
          }, 3000);
      },
    },
    created() {
      setTimeout(() => {
        this.loading = false;
      }, 100);
    },

    computed: {
      ...mapGetters({
        user: "auth/user",
      }),
      computedDateFormatted() {
        return this.formatDate(this.date);
      },
      date_picker() {
        if (this.date) return this.$moment(this.date).format("MMMM - YYYY");
      },
      tanggal_kegiatan() {
        if (this.waktu) return this.$moment(this.waktu).format("dddd, DD - MMMM - YYYY");
      },
      bulan() {
        return this.$moment(this.date).endOf("month").format("YYYY-MM-DD");
      },
    },
    methods: {
      formatDate(date) {
        if (!date) return null;

        const [year, month, day] = date.split("-");
        return `${day}/${month}/${year}`;
      },
      save() {
        this.dialogOverlay = true;
        this.$http
          .post("/camat", {
            instansi_id: this.user.instansi_id,
            waktu: this.waktu + " " + this.time,
            tempat: this.form.tempat,
            uraian_kegiatan: this.form.uraian,
            periode: this.date + "-01",
          })
          .then((response) => {
            let self = this;
            setTimeout(function () {
              self.dialogOverlay = false;
              self.$toast.success("Data Berhasil Disimpan");
              self.$router.push("/kegiatan-camat");
            }, 10 * 10 * 10);
          })
          .catch((error) => {
            this.dialogOverlay = false;
            if (error.response.status == 422) {
              this.$toast.error("Periksa Form Kembali");
            } else {
              this.$toast.error("Sudah terdapat laporan pada periode tersebut");
            }
          });
      },
      dateSelected(waktu) {
        if (waktu) {
          this.timeDisabled = false;
        } else {
          this.timeDisabled = true;
          this.time = "";
          this.waktu = "";
        }
      },
      dateDisabled(val) {
        if (val) {
          this.disabledDate = false;
        } else {
          this.waktu = "";
          this.time = "";
          this.loading = true;
          this.disabledDate = true;
          this.menu2 = false;
          setTimeout(() => {
            this.loading = false;
          }, 100);
        }
      },
    },
  };
</script>

<style scoped>
  .regist {
    padding-left: 80px;
    padding-right: 50px;
  }
  .form-right {
    padding-right: 90px;
    font-size: 20px;
  }
  .form-right-1 {
    margin-top: 60px;
    padding-right: 90px;
    font-size: 20px;
  }
  .form-credential {
    margin-top: 20px;
    padding-right: 90px;
  }

  .name {
    border-radius: 15px;
  }
  /* .v-btn:not(.v-btn--round).v-size--default {
  margin-top: 50px;
  background: #4662d4;
  color: white;
  box-sizing: content-box;
  border-radius: 25px;
  width: 111px;
  height: 45px;
  padding: 4px;
} */
  .btn {
    margin-top: 30px;
  }
  .form {
    border-style: none;
    text-decoration: none;
  }
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  input[type="number"] {
    -moz-appearance: textfield;
  }
</style>
