<template>
  <div>
    <div>
      <b-row>
        <b-col lg="6">
          <b-skeleton-wrapper :loading="loading">
            <template #loading>
              <b-skeleton></b-skeleton>
            </template>
            <b-card
              border-variant="primary"
              header="Total Kelurahan"
              header-bg-variant="primary"
              header-text-variant="white"
              align="center"
            >
              <b-card-text>{{ this.total_kelurahan }} Kelurahan</b-card-text>
            </b-card>
          </b-skeleton-wrapper>
        </b-col>
        <b-col lg="6">
          <b-skeleton-wrapper :loading="loading">
            <template #loading>
              <b-skeleton></b-skeleton>
            </template>
            <b-card
              border-variant="primary"
              header-html="Total Data yang Ditolak <b>(Bulan Ini)</b>"
              header-bg-variant="primary"
              header-text-variant="white"
              align="center"
            >
              <b-card-text>{{ this.notvalid }} Data</b-card-text>
            </b-card>
          </b-skeleton-wrapper>
        </b-col>
        <b-col lg="6">
          <b-skeleton-wrapper :loading="loading">
            <template #loading>
              <b-skeleton></b-skeleton>
            </template>
            <b-card
              border-variant="primary"
              header-html="Informasi Seluruh Kecamatan"
              header-bg-variant="primary"
              header-text-variant="white"
              header-tag="header"
              align="center"
            >
              <template #header>
                <!-- <v-badge inline :content="notif_all" :value="notif_all" color="red"> -->
                <p class="mt-0 mb-0">Informasi untuk seluruh Kecamatan</p>
                <!-- </v-badge> -->
              </template>
              <b-list-group v-for="(item, index) in all_informasi" :key="item.id">
                <b-list-group-item active class="text-left">
                  <p class="mb-1">
                    {{ item.informasi }}
                  </p>
                  <div class="d-flex w-100 justify-content-between">
                    <small class="text-right">{{
                      item.waktu | moment("dddd / DD - MMMM - YYYY")
                    }}</small>
                  </div>
                </b-list-group-item>
                <v-divider :key="index"></v-divider>
              </b-list-group>
            </b-card>
          </b-skeleton-wrapper>
        </b-col>
        <b-col lg="6">
          <b-skeleton-wrapper :loading="loading">
            <template #loading>
              <b-skeleton></b-skeleton>
            </template>
            <b-card
              border-variant="primary"
              header-tag="header"
              header-bg-variant="primary"
              header-text-variant="white"
              align="center"
            >
              <template #header>
                <!-- <v-badge inline :content="notif" :value="notif" color="red"> -->
                <p class="mt-0 mb-0">Informasi untuk {{ user.instansi.nama_instansi }}</p>
                <!-- </v-badge> -->
              </template>
              <b-list-group v-for="(item, index) in informasi" :key="item.id">
                <b-list-group-item active class="text-left">
                  <p class="mb-1">
                    {{ item.informasi }}
                  </p>
                  <div class="d-flex w-100 justify-content-between">
                    <small class="text-right">{{
                      item.waktu | moment("dddd / DD - MMMM - YYYY")
                    }}</small>
                  </div>
                </b-list-group-item>
                <v-divider :key="index"></v-divider>
              </b-list-group>
            </b-card>
          </b-skeleton-wrapper>
        </b-col>
        <b-col>
          <b-skeleton-wrapper :loading="loading">
            <template #loading>
              <b-skeleton></b-skeleton>
            </template>

            <b-card
              border-variant="primary"
              header-html="Total Seluruh Data yang Sudah Diinput <b>(Bulan Ini)</b>"
              header-bg-variant="primary"
              header-text-variant="white"
              align="center"
            >
              <!-- <column-chart
                :download="{ background: '#fff' }"
                :library="barchartOptions"
                loading="loading"
                label="Total Data yang Diinputkan dan Sudah Divalidasi"
                :data="[
                  ['PBB', 1],
                  ['PATEN', 2],
                  ['Kependudukan', 1],
                  ['Akta', 3],
                  ['Bencana Alam', 2],
                ]"
              ></column-chart> -->
              <column-chart
                :options="barchartOptions"
                v-if="loaded"
                :chartdata="this.datacollection"
              />
            </b-card>
          </b-skeleton-wrapper>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from "vuex";
  import ColumnChart from "../../components/BarChart.vue";

  export default {
    components: {
      ColumnChart,
    },
    data() {
      return {
        pbb: [],
        notif: 0,
        total_pbb: "",
        total_kelurahan: "",
        kelurahan: "",
        sekarang: "",
        waktu_lama: "",
        loaded: false,
        interval: null,
        waktu: new Date(),
        loading: false,
        pbb_invalid: "",
        pbb_valid: "",
        pbb_tolak: "",
        paten_invalid: "",
        paten_valid: "",
        paten_tolak: "",
        kependudukan_invalid: "",
        kependudukan_valid: "",
        kependudukan_tolak: "",
        camat_invalid: "",
        camat_valid: "",
        camat_tolak: "",
        bencana_invalid: "",
        bencana_valid: "",
        bencana_tolak: "",
        notvalid: "",
        informasi: "",
        notif_all: "",
        all_informasi: "",
        barchartOptions: {
          responsive: true,
          lineTension: 1,
          maintainAspectRatio: false,
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                  stepSize: 1,
                },
              },
            ],
            // xAxes: [
            //   {
            //     stacked: true,
            //     barPercentage: 0.7,
            //   },
            // ],
          },
          datasets: {
            bar: {
              categoryPercentage: 0.4,
            },
          },
        },
      };
    },
    computed: {
      ...mapGetters({
        authenticated: "auth/authenticated",
        user: "auth/user",
      }),
    },
    destroyed() {
      clearInterval(this.interval);
    },
    created() {
      this.renderData();
    },
    methods: {
      renderData() {
        this.loading = true;
        this.loaded = false;
        //GET DATA FOR TOTAL

        //GET DATA TOTAL KELURAHAN
        this.$http
          .get("/kelurahan", {
            params: {
              instansi_id: this.user.instansi_id,
            },
          })
          .then((response) => {
            this.total_kelurahan = response.data.total;
            // console.log(this.total_kelurahan);
            // this.kelurahan = response.data.data;
            this.firstLoad = false;
            this.dialog = false;
            this.dialogOverlay = false;
            this.overlay = true;
          })
          .catch((error) => {
            if (error) {
              console.log(error);
            }
          });

        // INFORMASI
        this.$http
          .get("/informasi", {
            params: {
              instansi_id: this.user.instansi_id,
            },
          })
          .then((response) => {
            this.informasi = response.data.data;
            this.notif = response.data.total;
          });

        this.$http
          .get("/informasi", {
            params: {
              instansi_id: 0,
            },
          })
          .then((response) => {
            this.all_informasi = response.data.data;
            this.notif_all = response.data.total;
          });

        //DATA BELUM VALID
        this.$http
          .get("/tolak", {
            params: {
              periode: this.$moment(this.waktu).format("YYYY-MM"),
              instansi_id: this.user.instansi_id,
            },
          })
          .then((response) => {
            this.notvalid = response.data.data;
            this.loading = false;
          });
        // TOTAL UNTUK CHART

        this.$http
          .get("/chart", {
            params: {
              periode: this.$moment(this.waktu).format("YYYY-MM"),
              instansi_id: this.user.instansi_id,
            },
          })
          .then((response) => {
            this.pbb_invalid = response.data.data[0].pbb_invalid;
            this.pbb_valid = response.data.data[1].pbb_valid;
            this.pbb_tolak = response.data.data[2].pbb_tolak;
            this.paten_invalid = response.data.data[3].paten_invalid;
            this.paten_valid = response.data.data[4].paten_valid;
            this.paten_tolak = response.data.data[5].paten_tolak;
            this.kependudukan_invalid = response.data.data[6].kependudukan_invalid;
            this.kependudukan_valid = response.data.data[7].kependudukan_valid;
            this.kependudukan_tolak = response.data.data[8].kependudukan_tolak;
            this.camat_invalid = response.data.data[9].camat_invalid;
            this.camat_valid = response.data.data[10].camat_valid;
            this.camat_tolak = response.data.data[11].camat_tolak;
            this.bencana_invalid = response.data.data[12].bencana_invalid;
            this.bencana_valid = response.data.data[13].bencana_valid;
            this.bencana_tolak = response.data.data[14].bencana_tolak;
            this.loaded = true;

            // UNTUK INFORMASI
            // console.log(this.$moment(test).fromNow());
            this.datacollection = {
              labels: ["PBB", "PATEN", "Kependudukan", "Kegiatan Camat", "Bencana Alam"],
              datasets: [
                {
                  label: "Belum Divalidasi",
                  backgroundColor: "rgba(54, 162, 235, 1)",
                  data: [
                    `${this.pbb_invalid}`,
                    `${this.paten_invalid}`,
                    `${this.kependudukan_invalid}`,
                    `${this.camat_invalid}`,
                    `${this.bencana_invalid}`,
                  ],
                  borderWidth: 1,
                },
                {
                  label: "Sudah Divalidasi",
                  backgroundColor: "rgba(255, 206, 86, 1)",
                  data: [
                    `${this.pbb_valid}`,
                    `${this.paten_valid}`,
                    `${this.kependudukan_valid}`,
                    `${this.camat_valid}`,
                    `${this.bencana_valid}`,
                  ],
                  borderWidth: 1,
                },
                {
                  label: "Ditolak",
                  backgroundColor: "rgba(233, 27, 54, 0.8)",
                  data: [
                    `${this.pbb_tolak}`,
                    `${this.paten_tolak}`,
                    `${this.kependudukan_tolak}`,
                    `${this.camat_tolak}`,
                    `${this.bencana_tolak}`,
                  ],
                  borderWidth: 1,
                },
              ],
            };
          });
      },
    },
  };
</script>
