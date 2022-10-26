const ApiService    = require("/Users/patrickbittner/plentymarkets/5.0.2/plugin-ceres/resources/js/src/app/services/ApiService.js");
const ModalService  = require("/Users/patrickbittner/plentymarkets/5.0.2/plugin-ceres/resources/js/src/app/services/ModalService");

let search = [];

$(document).ready(function () {

    const carSelectionModal = ModalService.findModal(document.getElementById('carSelectionModal'));
    const carSelectionModalBody = document.getElementById('carSelectionModalBody');


    carSelectionModal.on('hide.bs.modal', function(){
        carSelectionModalBody.innerHTML = '';
    });

    carSelectionModal.on('shown.bs.modal', function(){
        let body = '';
        for(let i in search){

            let cars = '';
            for(let c in search[i].list){

                if(search[i].list[c].hsn == 'hsn'){
                    cars = cars + '<div class="d-flex justify-content-between mt-1">' +
                        '<span>'+search[i].car.name+'</span>' +
                        '<button class="btn btn-set-car btn-primary" data-car="'+search[i].car.id+'" data-hsntsn="'+search[i].list[c].id+'">Wählen</button>' +
                        '</div>';
                } else {
                    cars = cars + '<div class="d-flex justify-content-between mt-1">' +
                        '<span>'+search[i].car.name+' (HSN/TSN: ' + search[i].list[c].hsn + ' ' + search[i].list[c].tsn + ')</span>' +
                        '<button class="btn btn-set-car btn-primary" data-car="'+search[i].car.id+'" data-hsntsn="'+search[i].list[c].id+'">Wählen</button>' +
                        '</div>';
                }


            }

            body = body + cars;
        }

        carSelectionModalBody.innerHTML += body;

        let btnSetCarElements = document.getElementsByClassName("btn-set-car");

        for(let y in btnSetCarElements){
            btnSetCarElements[y].addEventListener("click", event => {
                let carId = event.target.getAttribute('data-car');
                let hsntsnId = event.target.getAttribute('data-hsntsn');

                ApiService.post("/rest/car-parts-finder", {
                    carId: carId,
                    hsntsnId: hsntsnId,
                }).done(response => { location.reload(); });
            });
        }
    });
});


Vue.component("car-parts-finder-search", {
    template: "#vue-car-parts-finder-search",
    data: () => ({
        brands: [],
        brand: 0,
        models: [],
        model: 0,
        types: [],
        type: 0,
        platforms: [],
        platform: 0,
        search: [],
        hsn: "",
        tsn: "",
        loading: false,
        searchDisabled: false,
        carSelectionModal: null

    }),
    mounted()
    {
        this.$nextTick(() =>
        {
            this.carSelectionModal = ModalService.findModal(document.getElementById('carSelectionModal'));
        });
    },
    created() {
        this.indexBrands();
    },
    methods: {

        indexBrands(){
            this.loading = true;

            this.brands = [];
            this.brand = 0;
            this.models = [];
            this.model = 0;
            this.types = [];
            this.type = 0;
            this.platforms = [];
            this.platform = 0;
            this.search = [];

            ApiService.get("/rest/car-parts-finder/brands")
                .done(response =>
                {
                    this.brands = response;
                    this.loading = false;
                });
        },
        indexModels(){
            this.loading = true;

            this.models = [];
            this.model = 0;
            this.types = [];
            this.type = 0;
            this.platforms = [];
            this.platform = 0;
            this.search = [];

            if(this.brand > 0){
                ApiService.get("/rest/car-parts-finder/brands/"+this.brand+"/models")
                    .done(response =>
                    {
                        this.models = response;
                        this.loading = false;
                    });
            } else {
                this.loading = false;
            }

        },
        indexTypes(){
            this.loading = true;

            this.types = [];
            this.type = 0;
            this.platforms = [];
            this.platform = 0;
            this.search = [];

            if(this.model > 0){
                ApiService.get("/rest/car-parts-finder/brands/"+this.brand+"/models/"+this.model+"/types")
                    .done(response =>
                    {
                        this.types = response;
                        this.loading = false;
                    });
            } else {
                this.loading = false;
            }

        },
        indexPlatforms(){
            this.loading = true;

            this.platforms = [];
            this.platform = 0;
            this.search = [];

            if(this.type > 0){
                ApiService.get("/rest/car-parts-finder/brands/"+this.brand+"/models/"+this.model+"/types/"+this.type+"/platforms")
                    .done(response =>
                    {
                        this.platforms = response;
                        this.loading = false;
                    });
            } else {
                this.loading = false;
            }

        },

        searchCar(){
            this.loading = true;

            this.search = [];

            if(this.platform > 0){
                ApiService.get("/rest/car-parts-finder/search", {
                    platformId: this.platform,
                    hsn: this.hsn,
                    tsn: this.tsn,
                }).done(response =>
                {
                    this.search = response;
                    this.loading = false;

                    search = this.search;

                    this.carSelectionModal.show();
                });
            } else {
                this.loading = false;
            }
        },
        setCar(carId, hsntsnId = null){
            this.loading = true;

                ApiService.post("/rest/car-parts-finder", {
                    carId: carId,
                    hsntsnId: hsntsnId,
                }).done(response => { location.reload(); });
        },

        showCarSelectionModal(search) {
            this.$emit('toggleCarSelectionModal', search);
        }
    }
});


Vue.component("car-parts-finder-current", {
    template: "#vue-car-parts-finder-current",
    data: () => ({
        current: {
            car: null,
            hsntsn: null
        },
        loading: false
    }),
    created() {
        this.index();
    },
    methods: {

        index(){
            this.loading = true;

            ApiService.get("/rest/car-parts-finder")
                .done(response =>
                {
                    this.current = response.current;
                });

            this.loading = false;
        },

        unsetCar(){
            this.loading = true;

            ApiService.del("/rest/car-parts-finder")
                .done(() => { location.reload(); });
        }
    }
});
