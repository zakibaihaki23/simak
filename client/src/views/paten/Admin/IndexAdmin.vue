<template>
  <div id="app" style="margin-left: 25px; margin-right: 25px">
    <!-- FOR ALL DEVICE -->
    <v-container>
      <b-row>
        <b-col>
          <div>
            <h4>Laporan Rekapitulasi PATEN</h4>
          </div>
        </b-col>
        <b-col> </b-col>
      </b-row>
    </v-container>
    <v-divider class="d-flex d-none d-sm-block"></v-divider>

    <b-row style="margin-top: 1px" cols-lg="5" cols-md="1">
      <b-col>
        <KecamatanSelected
          v-show="!firstLoad"
          v-model="kecamatan"
          @selected="kecamatanSelected"
        ></KecamatanSelected>
      </b-col>
      <b-col>
        <KelurahanSelected
          v-show="!firstLoad"
          v-model="kelurahan"
          @selected="kelurahanSelected"
          :kecamatanId="kecamatan.value"
          :disabled="kelurahanDisabled"
        >
        </KelurahanSelected>
      </b-col>
      <b-col lg="3">
        <v-menu
          ref="menu"
          v-model="date_filter"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
          min-width="290px"
        >
          <template v-slot:activator="{ on }">
            <div v-on="on">
              <v-tooltip top>
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-show="!firstLoad"
                    v-bind="attrs"
                    v-on="on"
                    style="border-radius: 10px; font-size: 13px; width: 250px"
                    prepend-inner-icon="mdi-calendar"
                    outlined
                    single-line
                    clearable
                    readonly
                    dense
                    @click:clear="(date = ''), renderData(search)"
                    :value="format_date"
                  >
                    <template v-slot:label>Periode</template>
                  </v-text-field>
                </template>
                <span>Cari Berdasarkan Periode</span>
              </v-tooltip>
            </div>
          </template>
          <v-date-picker
            locale="id"
            v-model="date"
            type="month"
            no-title
            scrollable
            @input="(date_filter = false), renderData(search)"
          >
          </v-date-picker>
        </v-menu>
      </b-col>
      <b-col lg="1"></b-col>
      <b-col></b-col>
    </b-row>
    <br />
    <v-skeleton-loader
      v-if="firstLoad"
      :loading="isLoading"
      type="table-tbody"
      :types="{ 'table-row': 'table-cell@8' }"
    ></v-skeleton-loader>
    <v-progress-linear :active="isLoading" :indeterminate="isLoading" middle></v-progress-linear>
    <v-simple-table v-show="!firstLoad">
      <template v-slot:default>
        <thead style="border-style: solid; border-width: 2px; border-color: #d7d0d0">
          <td rowspan="2">Periode</td>
          <td rowspan="2">Kecamatan</td>
          <td rowspan="2">Desa/Kelurahan</td>
          <td colspan="13">Jumlah Pelayanan Non Perizinan</td>
          <td colspan="5">Jumlah Pelayanan Perizinan</td>
          <td rowspan="2">Keterangan</td>

          <tr>
            <th>Perekaman Data KTP-E</th>
            <th>Pengantar Kartu Keluraga (KK)</th>
            <th>Keterangan Pindah</th>
            <th>Keterangan Domisili</th>
            <th>Pengantar Akte Kelahiran</th>
            <th>Pencatatan Lahir/Mati</th>
            <th>Pencatatan Perkawinan</th>
            <th>Pencatatan Perceraian</th>
            <th>Pengantar Catatan Kepolisian<br />(SKCK)</th>
            <th>Keterangan Tidak Mampu<br />(SKTM)</th>
            <th>Rekomendasi</th>
            <th>Legalisasi Umum</th>
            <th>Keterangan/Pengantar Lainnya</th>
            <th>
              Izin Mendirikan Bangunan (IMB), <br />
              Luas &lt;100 m2
            </th>
            <th>Izin Usaha Perdagangan<br />(SITU/SIUP), Modal &lt;50.000.000</th>
            <th>
              Izin Merobohkan Bangunan,<br />
              Luas &lt;100 m2
            </th>
            <th>Izin Keramaian</th>
            <th>Izin Domisili Partai</th>
          </tr>
        </thead>
        <tbody v-if="paten == ''">
          <td colspan="21" style="font-weight: bold">No Data Available</td>
        </tbody>
        <tbody v-else>
          <tr v-for="item in paten" :key="item.id">
            <template>
              <td class="text-left" v-if="item.periode == null" style="font-weight: bold">Nihil</td>
              <td class="text-left" v-else>{{ item.periode | moment("MMMM - YYYY") }}</td>
            </template>
            <template>
              <td class="text-left">{{ item.nama_instansi }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.kelurahan == null" style="font-weight: bold">
                Nihil
              </td>
              <td class="text-left" v-else>{{ item.kelurahan }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.perekaman_ktp == null" style="font-weight: bold">
                Nihil
              </td>
              <td v-else>{{ item.perekaman_ktp }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.pengantar_kk == null" style="font-weight: bold">
                Nihil
              </td>
              <td v-else>{{ item.pengantar_kk }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.ket_pindah == null" style="font-weight: bold">
                Nihil
              </td>
              <td v-else>{{ item.ket_pindah }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.ket_domisili == null" style="font-weight: bold">
                Nihil
              </td>
              <td v-else>{{ item.ket_domisili }}</td>
            </template>
            <template>
              <td
                class="text-left"
                v-if="item.pengantar_akta_lahir == null"
                style="font-weight: bold"
              >
                Nihil
              </td>
              <td v-else>{{ item.pengantar_akta_lahir }}</td>
            </template>
            <template>
              <td
                class="text-left"
                v-if="item.pengantar_akta_mati == null"
                style="font-weight: bold"
              >
                Nihil
              </td>
              <td v-else>{{ item.pengantar_akta_mati }}</td>
            </template>
            <template>
              <td
                class="text-left"
                v-if="item.pencatatan_perkawinan == null"
                style="font-weight: bold"
              >
                Nihil
              </td>
              <td v-else>{{ item.pencatatan_perkawinan }}</td>
            </template>
            <template>
              <td
                class="text-left"
                v-if="item.pencatatan_perceraian == null"
                style="font-weight: bold"
              >
                Nihil
              </td>
              <td v-else>{{ item.pencatatan_perceraian }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.skck == null" style="font-weight: bold">Nihil</td>
              <td v-else>{{ item.skck }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.sktm == null" style="font-weight: bold">Nihil</td>
              <td v-else>{{ item.sktm }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.rekomendasi == null" style="font-weight: bold">
                Nihil
              </td>
              <td v-else>{{ item.rekomendasi }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.legalisasi == null" style="font-weight: bold">
                Nihil
              </td>
              <td v-else>{{ item.legalisasi }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.ket_lain == null" style="font-weight: bold">
                Nihil
              </td>
              <td v-else>{{ item.ket_lain }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.imb == null" style="font-weight: bold">Nihil</td>
              <td v-else>{{ item.imb }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.situ_siup == null" style="font-weight: bold">
                Nihil
              </td>
              <td v-else>{{ item.situ_siup }}</td>
            </template>
            <template>
              <td
                class="text-left"
                v-if="item.izin_merobohkan_bangunan == null"
                style="font-weight: bold"
              >
                Nihil
              </td>
              <td v-else>{{ item.izin_merobohkan_bangunan }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.izin_keramaian == null" style="font-weight: bold">
                Nihil
              </td>
              <td v-else>{{ item.izin_keramaian }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.izin_partai == null" style="font-weight: bold">
                Nihil
              </td>
              <td v-else>{{ item.izin_partai }}</td>
            </template>
            <template>
              <td class="text-left" v-if="item.keterangan == null" style="font-weight: bold">
                Nihil
              </td>
              <td
                v-else
                style="width: 100%; white-space: normal; vertical-align: center !important"
              >
                {{ item.keterangan }}
              </td>
            </template>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
    <div class="mt-5">
      <b-pagination
        align="center"
        v-model="pagination.current_page"
        :total-rows="pagination.total"
        @input="onPageChange"
        :per-page="data.per_page"
        first-number
        last-number
      ></b-pagination>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from "vuex";
  import KecamatanSelected from "../../../components/SelectKecamatan.vue";
  import KelurahanSelected from "../../../components/SelectKelurahanAdmin.vue";

  export default {
    components: { KecamatanSelected, KelurahanSelected },
    data() {
      return {
        search: "",
        searchBox: "",
        date_filter: "",
        date: "",
        isMobile: false,
        dialog: false,
        validDialog: false,
        tolakDialog: false,
        firstLoad: true,
        loadingBtn: false,
        isLoading: true,
        loading: false,
        dialogOverlay: false,
        overlay: false,
        kelurahans: "",
        data: [],
        pagination: {
          current_page: 1,
          total: 0,
        },
        kel: "",
        idData: "",
        id: "",
        period: "",
        status: null,
        filterActive: null,
        kelurahanDisabled: true,
        paten: [],
        kecamatan: [],
        kelurahan: null,
        status: null,
        filterStatus: null,
        perr: "",
        kell: "",
        filterKelurahan: null,
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
          }, 1000);
      },
    },
    created() {
      this.renderData();
    },
    watch: {
      clearable: {
        handler: function (val) {
          this.renderData("");
        },
        deep: true,
      },
    },
    computed: {
      ...mapGetters({
        user: "auth/user",
      }),
      format_date() {
        if (this.date) return this.$moment(this.date).format("MMMM - YYYY");
      },
    },
    methods: {
      formatPrice(value) {
        let val = (value / 1).toFixed(0).replace(".", ",");
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      },
      onPageChange() {
        this.renderData();
      },
      renderData(search) {
        this.isLoading = true;

        let periode = "";
        if (this.date) {
          periode = this.date;
        } else {
          periode = null;
        }
        // UNTUK USER

        this.$http
          .get("/paten", {
            params: {
              page: this.pagination.current_page,
              instansi_id: this.kecamatan.value,
              kelurahan: this.filterKelurahan,
              periode,
              is_verified: 2,
            },
          })
          .then((response) => {
            this.paten = response.data.data.data;
            this.firstLoad = false;
            this.dialog = false;
            this.dialogOverlay = false;
            this.overlay = true;
            this.isLoading = false;
            this.pagination.current_page = response.data.data.current_page;
            this.pagination.total = response.data.data.total;
            this.data = response.data.data;
          })
          .catch((error) => {
            if (error) {
              console.log(error);
            }
          });
      },
      kecamatanSelected(kecamatan) {
        this.kecamatan = "";
        this.filterKecamatan = null;
        this.date_filter = false;
        if (kecamatan) {
          this.kelurahanDisabled = false;
          this.kecamatan = kecamatan;
          this.filterKecamatan = kecamatan.id;
        } else {
          this.kelurahanDisabled = true;
        }
        this.renderData();
      },
      kelurahanSelected(val) {
        this.val = null;
        this.filterKelurahan = "";
        if (val) {
          this.val = val;
          this.filterKelurahan = val.name;
        } else {
          this.val = null;
          this.filterKelurahan = "";
        }
        this.renderData();
      },
      dateSelected() {
        if (this.user.role == "User") {
          if (this.date) {
            this.downloadDisabled = false;
          } else {
            this.downloadDisabled = true;
          }
        } else {
          if (this.kecamatan.value) {
            this.downloadDisabled = false;
          }
          if (this.date == "") {
            this.downloadDisabled = true;
          }
        }
      },
    },
  };
</script>

<style scoped>
  .v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
    font-size: 17px;
    margin: auto;
    white-space: nowrap;
  }

  tbody td {
    text-align: center;
    border-style: solid;
    border-width: 2px;
    border-color: #d7d0d0;
  }
  thead tr th {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell,
      "Open Sans", "Helvetica Neue", sans-serif;
    font-weight: bold;
    color: black !important;
    font-size: 10pt !important;
    text-align: center !important;
    border-style: solid;
    border-width: 2px;
    border-color: #d7d0d0;
  }
  thead td {
    color: black !important;
    font-weight: bold;
    text-align: center;
    border-style: solid;
    border-width: 2px;
    border-color: #d7d0d0;
    font-weight: bold;
  }
</style>
