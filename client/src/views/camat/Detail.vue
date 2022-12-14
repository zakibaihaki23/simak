<template>
  <div class="helper">
    <v-row>
      <v-col md="12">
        <h1>Edit Laporan Kegiatan Harian Camat</h1>
      </v-col>
    </v-row>

    <br />
    <div class="row">
      <div class="col mx-auto" style="font-size: 17pt">
        <div class="form-group row">
          <b-skeleton-wrapper :loading="loading">
            <template #loading>
              <b-skeleton></b-skeleton>
            </template>
            <div class="col-lg-2">
              <span>Kecamatan</span>
            </div>
            <div class="col-lg-6">
              <p style="font-weight: bold" v-if="camat.nama_instansi != null">
                {{ camat.nama_instansi }}
              </p>
              <p style="font-weight: bold" v-else>-</p>
            </div>
          </b-skeleton-wrapper>
        </div>

        <div class="form-group row">
          <b-skeleton-wrapper :loading="loading">
            <template #loading>
              <b-skeleton></b-skeleton>
            </template>
            <div class="col-lg-2">
              <p>Periode</p>
            </div>
            <div class="col-lg-6">
              <p style="font-weight: bold">
                {{ this.$moment(camat.periode).format("MMMM - YYYY") }}
              </p>
            </div>
          </b-skeleton-wrapper>
        </div>
      </div>
    </div>
    <v-form style="margin-top: 50px">
      <div class="row">
        <v-checkbox
          class="mt-0"
          v-model="nihil"
          true-value="null"
          false-value="0"
          :label="`Tidak ada laporan (NIHIL)`"
          @change="checkBox"
        >
        </v-checkbox>
        <div class="col mx-auto">
          <div class="form-group row">
            <div class="col-lg-6">
              <p>Tanggal Kegiatan</p>
              <v-menu
                ref="menu2"
                v-model="menu2"
                :close-on-content-click="false"
                :return-value.sync="form.tanggal"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
                :disabled="nihil == 'null'"
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
                    v-on="on"
                    @input="dateSelected"
                    :disabled="nihil == 'null'"
                  ></v-text-field>
                </template>
                <v-date-picker
                  @change="dateSelected"
                  locale="id"
                  v-model="form.tanggal"
                  no-title
                  scrollable
                  @input="$refs.menu2.save(form.tanggal)"
                  :min="camat.periode"
                  :max="bulan"
                  :show-current="camat.periode"
                >
                </v-date-picker>
              </v-menu>
            </div>
            <div class="col-lg-6" v-if="camat.waktu == null">
              <p>Waktu Kegiatan</p>
              <v-menu
                ref="menu3"
                v-model="menu3"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="form.time"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
                :disabled="timeDisabled || nihil == 'null'"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="form.time"
                    prepend-inner-icon="mdi-clock"
                    single-line
                    readonly
                    clearable
                    outlined
                    class="form"
                    v-bind="attrs"
                    v-on="on"
                    :disabled="timeDisabled || nihil == 'null'"
                  ></v-text-field>
                </template>
                <v-time-picker
                  locale="id"
                  format="24hr"
                  v-if="menu3"
                  v-model="form.time"
                  full-width
                  @click:minute="$refs.menu3.save(form.time)"
                ></v-time-picker>
              </v-menu>
            </div>
            <div class="col-lg-6" v-else>
              <p>Waktu Kegiatan</p>
              <v-menu
                ref="menu3"
                v-model="menu3"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="form.waktu"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
                :disabled="nihil == 'null'"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="form.waktu"
                    prepend-inner-icon="mdi-clock"
                    single-line
                    readonly
                    clearable
                    outlined
                    class="form"
                    v-bind="attrs"
                    v-on="on"
                    :disabled="nihil == 'null'"
                  ></v-text-field>
                </template>

                <v-time-picker
                  locale="id"
                  format="24hr"
                  v-if="menu3"
                  v-model="form.waktu"
                  full-width
                  @click:minute="$refs.menu3.save(form.waktu)"
                ></v-time-picker>
              </v-menu>
            </div>
          </div>
          <div class="form-group row">
            <div>
              <p>Tempat Kegiatan</p>
              <v-text-field
                :disabled="nihil == 'null'"
                outlined
                class="form"
                v-model="form.tempat"
                clearable
              ></v-text-field>
            </div>
          </div>
          <div class="form-group row">
            <div>
              <p>Uraian Kegiatan</p>
              <v-textarea
                :disabled="nihil == 'null'"
                clearable
                clear-icon="mdi-close-circle"
                single-line
                outlined
                class="form"
                v-model="form.uraian"
              ></v-textarea>
            </div>
          </div>
        </div>
      </div>

      <br />
      <br /><br />

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
                margin-top: 50px;
                box-sizing: content-box;
                border-radius: 25px;
                width: 111px;
                height: 45px;
                padding: 4px;
              "
              >Batalkan</v-btn
            >
            <v-btn
              style="
                margin-top: 50px;
                background: #007bff;
                color: white;
                box-sizing: content-box;
                border-radius: 25px;
                width: 111px;
                height: 45px;
                padding: 4px;
                margin-left: 10px;
              "
              class="save"
              @click="save"
              :disabled="overlay == true || camat.is_verified != 3"
              >Simpan</v-btn
            >
          </b-col>
        </b-row>
      </div>
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
    </v-form>
    <br />
    <br />
  </div>
</template>

<script>
  export default {
    data() {
      return {
        pbb: [],
        overlay: false,
        camat: [],
        menu: false,
        menu2: false,
        menu3: false,
        loading: false,
        nihil: null,
        dialogOverlay: false,
        form: {
          kecamatan: "",
          periode: "",
          tanggal: "",
          time: "",
          tempat: "",
          uraian: "",
          waktu: "",
        },
        kosong: "",
        placeholder: " ",
        readonly: false,
        disabled: false,
        outlined: true,
        clearable: true,
        status: [],
        timeDisabled: true,
      };
    },
    watch: {
      search: {
        handler: function (val) {
          this.renderData(val);
        },
        deep: true,
      },
    },

    created() {
      this.renderData();
    },
    computed: {
      date_picker() {
        if (this.form.date) return this.$moment(this.form.date).format("MMMM - YYYY");
      },
      tanggal_kegiatan() {
        if (this.form.tanggal)
          return this.$moment(this.form.tanggal).format("dddd, DD - MMMM - YYYY");
      },
      waktu_kegiatan() {
        if (this.form.time) return this.$moment(this.form.time);
      },
      bulan() {
        return this.$moment(this.camat.periode).endOf("month").format("YYYY-MM-DD");
      },
    },

    methods: {
      renderData() {
        this.loading = true;
        this.$http
          .get("/camat/" + this.$route.params.id)

          .then((response) => {
            this.camat = response.data.data;
            this.form.tanggal = response.data.data.waktu;
            this.form.time = response.data.data.waktu;
            this.form.waktu = this.$moment(response.data.data.waktu).format("HH:mm");
            this.form.tempat = response.data.data.tempat;
            this.form.uraian = response.data.data.uraian_kegiatan;
            this.loading = false;
          });
      }, // CLOSE RENDER DATA
      dateSelected(tanggal) {
        this.menu2 = false;
        if (tanggal) {
          this.timeDisabled = false;
        } else {
          this.timeDisabled = true;
          this.time = "";
          this.tanggal = "";
        }
      },
      save() {
        if (this.camat.waktu == null && this.nihil != "null") {
          this.dialogOverlay = true;
          this.$http
            .put("/camat/" + this.$route.params.id, {
              instansi_id: this.camat.instansi_id,
              waktu: this.$moment(this.form.tanggal).format("YYYY-MM-DD") + " " + this.form.time,
              tempat: this.form.tempat,
              uraian_kegiatan: this.form.uraian,
              periode: this.camat.periode,
              is_verified: 1,
            })
            .then((response) => {
              let self = this;
              setTimeout(function () {
                self.dialogOverlay = false;
                self.$router.push("/kegiatan-camat");
                self.$toast.success("Data Berhasil Diupdate");
              }, 10 * 10 * 10);
            });
        }
        if (this.camat.waktu != null && this.nihil != "null") {
          this.dialogOverlay = true;
          this.$http
            .put("/camat/" + this.$route.params.id, {
              instansi_id: this.camat.instansi_id,
              waktu: this.$moment(this.form.tanggal).format("YYYY-MM-DD") + " " + this.form.waktu,
              tempat: this.form.tempat,
              uraian_kegiatan: this.form.uraian,
              periode: this.camat.periode,
              is_verified: 1,
            })
            .then((response) => {
              let self = this;
              setTimeout(function () {
                self.dialogOverlay = false;
                self.$router.push("/kegiatan-camat");
                self.$toast.success("Data Berhasil Diupdate");
              }, 10 * 10 * 10);
            });
        }
        if (this.nihil == "null") {
          this.dialogOverlay = true;
          this.$http
            .put("/camat/" + this.$route.params.id, {
              instansi_id: this.camat.instansi_id,
              waktu: null,
              tempat: this.form.tempat,
              uraian_kegiatan: this.form.uraian,
              periode: this.camat.periode,
              is_verified: 1,
            })
            .then((response) => {
              let self = this;
              setTimeout(function () {
                self.dialogOverlay = false;
                self.$router.push("/kegiatan-camat");
                self.$toast.success("Data Berhasil Diupdate");
              }, 10 * 10 * 10);
            });
        }
        // console.log(this.form.waktu);
      },
      checkBox(val) {
        if (val == "null") {
          this.form.tanggal = "";
          this.form.time = "";
          this.form.waktu = "";
          this.form.tempat = "";
          this.form.uraian = "";
        }
        if (val == "0") {
          this.renderData();
        }
      },
    }, // CLOSE METHODS
  };
</script>

<style scoped>
  .xlsx-button {
    border-radius: 30%;
    width: 200px;
  }
  .helper {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell,
      "Open Sans", "Helvetica Neue", sans-serif;
    padding-left: 80px;
    padding-right: 50px;
  }
  .v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
    font-size: 17px;
  }

  thead {
    border-radius: 60px;
  }
  .form {
    border-style: none;
    text-decoration: none;
  }
  .search {
    padding-left: 100px;
    padding-right: 50px;
    margin-top: 50px;
  }
  .search2 {
    margin-top: 150px;
    margin-right: 150px;
    box-sizing: content-box;
    width: 150px;
  }
  thead[data-v-8056b2e8] {
    background: #f0f2f7;
    border: 1px solid #dee2e6;
    box-sizing: border-box;
    border-radius: 20px;
  }
  /* .v-btn:not(.v-btn--round).v-size--default {
  position: absolute;
  width: 200px;
  font-size: 16px;
  height: 50px;
  background: #4662d4;
  font-weight: bold;
  color: white;
  border-radius: 25px;
  margin-top: 50px;
  text-transform: capitalize;
  cursor: pointer;
  padding: 5px;
} */

  /* .cancel {
    margin-top: 50px;
    background: #4662d4;
    color: white;
    box-sizing: content-box;
    border-radius: 25px;
    width: 111px;
    height: 45px;
  } */
  .button {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell,
      "Open Sans", "Helvetica Neue", sans-serif;
    font-weight: bold;
    vertical-align: middle;
    align-content: center;
    text-align: center;
  }
</style>
