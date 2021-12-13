console.log($t);

let app = new Vue({
    el: "#wp-trader-app",
    data() {
        return {
            render: false,
            tab: 0,
            details: -1,
            editRow: -1,
            tabs: ["Dashboard", "Settings", "Details"],
            title: "Title of the Dashboard",
            headerSetting:[
                { text: "Color" , value: "color", align: "center" },
                { text: "Rate" , value: "rate", align: "center" },
                { text: "Inversion Mínima" , value: "investmin", align: "center" },
                { text: "Inversion Máxima" , value: "investmax", align: "center" },
                { text: "Accion" , value: "action", align: "center" }
            ],
            rates: [
                {
                    color: "#fff",
                    rate: 0,
                    investmin: 0,
                    investmax: 0
                }
            ],
            newRate: {
                color: "#fff",
                rate: 0,
                investmin: 0,
                investmax: 0
            },
            headers: [
                { text: "Accion" , value: "accion", align: "center" }
            ],
            temp: {
                id: "",
                nombre: "",
                apellido: "",
                cedula: "",
                correo: "",
                pais: "",
                postalcode: "",
                telefono: "",
                monto: ""
            },
            users: [],
            countries: [],
            message: "Esta funcionando",            
        }
    },
    async created(){
        Object.entries( $t.fields ).forEach( 
            field =>
            { 
                this.headers.unshift( { 
                    text: field[1],
                    value: field[0],
                    align: "center",
                    sortable: false
                } );
            }
        )
        
        this.countries = JSON.parse( localStorage.getItem("wpt_countries") ) || [];
        this.countries.forEach( 
            ( country, $index ) => {
                this.countries[$index].label = country.name.common
            }
        )
        this.countries = this.countries.filter( country => {
            return country.enable
        } )
        this.users = $t.users;
        await this.getData();
    },
    methods: {
        createRate(){
            this.rates.push(this.newRate);
            this.newRate = {
                color: "#fff",
                rate: 0,
                investmin: 0,
                investmax: 0
            };
        },
        deleteRate( $index ){
            this.rates = this.rates.filter( 
                ( rate, index ) =>
                {
                    return index != $index;
                }
            )
        },
        detailsMode(tab) {
            if(this.details == -1){
                return ! (this.tabs.indexOf(tab) == 2);
            }
            else{
                return (this.tabs.indexOf(tab) == 2);
            }
        },
        async getData(){
            this.users.forEach( 
                (user, index) => {
                    this.users[ index ].monto = $t.getMountAll( user.id )
                    this.users[ index ].edit = false;
                }
            )
        },
        view(index) {
            if( this.details == -1 ) {
                this.details = index;
                this.temp = this.users[index]
                this.tab = 2;
            }else{ 
                this.details = -1;
                this.tab = 0;
            }
        },
        edit( $index ) {
            console.log( "Edit "+$index )
            this.editRow = $index            
        },
        async save( $index ) {
            console.log( $index, this.users[ $index ] )

            let dataSend = new FormData();
            dataSend.append('action', 'wpt_save_data');
            dataSend.append('index', $index);
            dataSend.append('value', JSON.stringify( this.users[ $index ] ) );
            const { status, statusText, data } = await axios.post(ajaxurl, dataSend)
            console.log( data )
            
            console.log( data )
            this.editRow = -1            
        },
        del() {
            console.log("del");
        },
        getColor(monto){
            console.log(monto)
            if(monto<100) return "#00ff00";
            else if(monto<200) return "#ffff00";
            else return "#ff3333";
        }

    },
    vuetify: new Vuetify()
});