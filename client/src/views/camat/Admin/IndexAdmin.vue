<template>
  <div id="app" style="margin-left: 25px; margin-right: 25px">
    <!-- FOR ALL DEVICE -->
    <v-container>
      <b-row>
        <b-col>
          <div>
            <h4>Laporan Kegiatan Harian Camat</h4>
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
                    @input="dateSelected"
                  >
                    <template v-slot:label>Filter Periode</template>
                  </v-text-field>
                </template>
                <span>Cari Berdasarkan Periode</span>
              </v-tooltip>
            </div>
          </template>
          <v-date-picker
            @change="dateSelected"
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
    <v-overlay :value="overlay">
      <v-progress-circular indeterminate color="blue"></v-progress-circular>
    </v-overlay>
    <v-data-table
      loading-text="Memuat Data"
      v-show="!firstLoad"
      :loading="isLoading"
      :headers="table"
      :search="search"
      :items="camat"
      :hide-header="isMobile"
      :class="{ mobile: isMobile }"
      hide-default-footer
      class="elevation-1"
    >
      <template v-slot:[`item.periode`]="{ item }">
        <div v-if="item.periode == '0000-00-00'">NIHIL</div>
        <div v-else>{{ item.periode | moment("MMMM - YYYY") }}</div>
      </template>
      <template v-slot:[`item.waktu`]="{ item }">
        <div v-if="item.waktu == null">NIHIL</div>
        <div v-else>
          {{ item.waktu | moment("dddd/DD-MMMM-YYYY HH:MM") }}
        </div>
      </template>
      <template v-slot:[`item.tempat`]="{ item }">
        <div v-if="item.tempat == null">NIHIL</div>
        <div v-else>
          {{ item.tempat }}
        </div>
      </template>
      <template v-slot:[`item.uraian_kegiatan`]="{ item }">
        <div v-if="item.uraian_kegiatan == null">NIHIL</div>
        <div v-else>
          {{ item.uraian_kegiatan }}
        </div>
      </template>
    </v-data-table>
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
        firstLoad: true,
        loadingBtn: false,
        isLoading: true,

        loading: false,
        overlay: false,
        data: [],
        pagination: {
          current_page: 1,
          total: 0,
        },
        kelurahans: "",
        kel: "",
        idData: "",
        id: "",
        period: "",
        table: [
          {
            text: "Periode Laporan",
            value: "periode",
            class: "black--text subheading",
            width: "140px",
          },
          {
            text: "Kecamatan",
            value: "nama_instansi",
            class: "black--text subheading",
            width: "150px",
          },
          {
            text: "Waktu Kegiatan",
            value: "waktu",
            class: "black--text subheading",
            width: "200px",
          },
          {
            text: "Tempat Kegiatan",
            value: "tempat",
            class: "black--text subheading",
          },
          {
            text: "Uraian Kegiatan",
            value: "uraian_kegiatan",
            class: "black--text subheading",
          },
          {
            value: "actions",
            sortable: false,
          },
        ],

        dataTable: [],
        warehouse: null,
        warehouse_id: "",
        type: "",
        status: null,
        filterActive: null,
        warehouseList: [],
        kecamatan: [],
        camat: [],
        kelurahan: [],
        kelurahanDisabled: true,
        downloadDisabled: true,
        status: null,
        filterStatus: null,
        kell: "",
        perr: "",
        idData: "",
        id: "",
        period: "",
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
    // mounted() {
    //   this.renderData('', this.status)
    // },
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
        let kelurahan = "";
        if (this.filterKelurahan || this.filterKelurahan == 0) {
          kelurahan = this.filterKelurahan;
        } else {
          kelurahan = null;
        }

        let is_verified = "";
        if (this.filterStatus || this.filterStatus == 0) {
          is_verified = this.filterStatus;
        } else {
          is_verified = null;
        }

        let periode = "";
        if (this.date) {
          periode = this.date;
        } else {
          periode = null;
        }
        // UNTUK USER
        this.$http
          .get("/camat", {
            params: {
              page: this.pagination.current_page,
              instansi_id: this.kecamatan.value,
              kelurahan: this.filterKelurahan,
              periode,
              is_verified: 2,
            },
          })
          .then((response) => {
            this.camat = response.data.data.data;
            this.firstLoad = false;
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
          // if (this.kecamatanSelected) {
          //   console.log("Masuk guyss");
          // }
        }
        // if (this.date == "") {
        //   this.downloadDisabled = true;
        // }
        // if (date) {
        //   this.date_filter = false;
        //   this.downloadDisabled = false;
        // }
        // if (date == null) {
        //   this.downloadDisabled = true;
        // }
        // this.renderData("");
      },
      // kelurahanSelected(kelurahan) {
      //   this.kelurahan = "";
      //   this.filterKelurahan = null;
      //   if (kelurahan) {
      //     this.kelurahan = kelurahan;
      //     this.filterKelurahan = kelurahan.id;
      //   }
      //   this.renderData();
      // },
    },
  };
</script>

<style scoped lang="scss">
  // .mytable table tr {
  //   border: none;
  // }
  // .v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  //   font-size: 17px;
  // }
  .helper {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell,
      "Open Sans", "Helvetica Neue", sans-serif;
    padding-left: 80px;
    padding-right: 50px;
  }

  // .v-btn:not(.v-btn--round).v-size--default {
  //   position: absolute;
  //   width: 200px;
  //   height: 50px;
  //   background: #4662d4;
  //   color: white;
  //   border-radius: 30px;
  //   font-size: 16px;
  //   margin-top: 50px;
  //   font-weight: bold;
  //   text-transform: capitalize;
  //   cursor: pointer;
  //   padding: 5px;
  // }
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
  .v-menu__content {
    border-radius: 8px;
    border: 1px solid #c4c4c4;
    outline-style: inherit;
    outline-color: white;
    box-shadow: none;

    outline-color: #e8eff2;
  }
  .v-sheet.v-list {
    background: #e8eff2;
  }
  .mobile {
    color: #333;
  }

  @media screen and (max-width: 768px) {
    .mobile table.v-table tr {
      max-width: 100%;
      position: relative;
      display: block;
    }

    .mobile table.v-table tr:nth-child(odd) {
      border-left: 6px solid deeppink;
    }

    .mobile table.v-table tr:nth-child(even) {
      border-left: 6px solid cyan;
    }

    .mobile table.v-table tr td {
      display: flex;
      width: 100%;
      border-bottom: 1px solid #f5f5f5;
      height: auto;
      padding: 10px;
    }

    .mobile table.v-table tr td ul li:before {
      content: attr(data-label);
      padding-right: 0.5em;
      text-align: left;
      display: block;
      color: #999;
    }
    .v-datatable__actions__select {
      width: 50%;
      margin: 0px;
      justify-content: flex-start;
    }
    .mobile .theme--light.v-table tbody tr:hover:not(.v-datatable__expand-row) {
      background: transparent;
    }
  }
  .flex-content {
    padding: 0;
    margin: 0;
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    width: 100%;
  }

  .flex-item {
    padding: 5px;
    width: 50%;
    height: 60px;
  }
</style>
