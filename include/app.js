console.log($t);

let app = new Vue({
    el: "#wp-trader-app",
    data() {
        return {
            render: false,
            tab: 0,
            tiempoCobro: 180,
            details: -1,
            editRow: -1,
            tabs: ["Dashboard", "Settings", "Details"],
            title: "Title of the Dashboard",
            headerInvesment: [
                { text: "Fecha de Inicio" , value: "fecha", align: "center" },
                { text: "Fecha de Cobro" , value: "fechacobro", align: "center" },
                { text: "Monto" , value: "monto", align: "center" },
                { text: "Dias para cobrar" , value: "cobro", align: "center" },
                { text: "Accion" , value: "action", align: "center" }
            ],
            investments: [],
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
                monto: 0
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
    filters: {
        date: value => {
            let valueArray = value.split("-");
            return [
                "Enero", "Febrero", "Marzo",
                "Abril", "Mayo", "Junio",
                "julio", "Agosto", "Septiembre",
                "Octubre", "Noviembre", "Diciembre",
            ][valueArray[0]]+", "+valueArray[1]+" del "+valueArray[2];
        }
    },
    methods: {
        cobrar(){

        },
        createRate(){
            this.rates.push(this.newRate);
            this.newRate = {
                id: -1,
                color: "#fff",
                rate: 0,
                investmin: 0,
                investmax: 0
            };
            axios.post
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
                this.investments = $t.getInvestments( this.users[ index ].id );
                this.investments.forEach(
                    ( investment, $index ) =>
                    {
                        let hoyms = (new Date()).getTime();
                        let fechams = (new Date( investment.fecha )).getTime();
                        let fechacobroms = fechams + this.tiempoCobro*1000*60*60*24;
                        let fechacobro = new Date( fechacobroms );
                        this.investments[ $index ].fechacobro = (fechacobro.getMonth() + 1)+"-"+fechacobro.getDate()+"-"+fechacobro.getFullYear();
                        this.investments[ $index ].cobro = parseInt( ( fechacobroms - hoyms )/1000/60/60/24 );

                    }
                )
            }else{ 
                this.details = -1;
                this.tab = 0;
            }
        },
        addContent( type, data, id ) {
            switch( type ) {
                case 'wpt_users': 
                    if( id != -1 ){
                        let index = -1;
                        this.users.forEach( 
                            (user, $index) => 
                            {
                                if( user.id == id ) index = $index;
                            }
                        );
                        this.users[index] = data;
                    }else{
                        this.users.push( data );
                    }
                    break;
                case 'wpt_investments': 
                    if( id != -1 ){
                        let index = -1;
                        this.investments.forEach( 
                            (investment, $index) => 
                            {
                                if( investment.id == id ) index = $index;
                            }
                        );
                        this.investments[index] = data;
                    }else{
                        this.investments.push( data );
                    }
                    break;
            }
        },
        content( type ){
            switch( type ) {
                case 'wpt_users': 
                    let max = 0;
                    this.users.forEach( 
                        user => 
                        {
                            if( user.id > max ) max = user.id;
                        }    
                    );
                    this.temp.id = max + 1;
                    return this.temp;
                case 'wpt_investments': return this.newRate;
            }

        },
        async edit( $index ) {
            console.log( "Edit "+$index )
            this.editRow = $index
        },
        async save( type, $index ){
            let dataSend = new FormData();
            dataSend.append('action', 'wpt_save_data');
            dataSend.append('target', type);
            dataSend.append('index', $index);
            if( $index == -1 ){
                dataSend.append('value', JSON.stringify( this.content( type ) ) );
            }else{
                if( type == 'wpt_users') dataSend.append('value', JSON.stringify( this.users.filter( user => user.id == $index )[0] ) );
                else dataSend.append('value', JSON.stringify( this.rate.filter( rate => rate.id == $index )[0] ) );
            }
            const { data } = await axios.post(ajaxurl, dataSend);
            console.log( "data : ", data );
            if( data ) {
                this.addContent( type, data, $index );
            }
            this.editRow = -1;
            this.newRate = {
                id: "",
                nombre: "",
                apellido: "",
                cedula: "",
                correo: "",
                pais: "",
                postalcode: "",
                telefono: "",
                monto: 0
            };
            this.temp = {
                id: -1,
                color: "#fff",
                rate: 0,
                investmin: 0,
                investmax: 0
            }
            this.render = ! this.render;
        },
        async del(type, $index) {
            let dataSend = new FormData();
            dataSend.append('action', 'wpt_delete_data');
            dataSend.append('target', type);
            dataSend.append('index', $index);
            const { data } = await axios.post(ajaxurl, dataSend);
            console.log( "data : ", data );
            if( data ) {
                if( type == 'wpt_users') {
                    console.log(this.users)
                    this.users = this.users.filter( user => user.id != $index );
                }else{
                    this.rates = this.rates.filter( rate => rate.id != $index );
                }
            }
            this.newRate = {
                id: "",
                nombre: "",
                apellido: "",
                cedula: "",
                correo: "",
                pais: "",
                postalcode: "",
                telefono: "",
                monto: 0
            };
            this.temp = {
                id: -1,
                color: "#fff",
                rate: 0,
                investmin: 0,
                investmax: 0
            }
            this.render = ! this.render;
        },
        getColor(monto){
            console.log("Monto : ",monto)
            if(monto<100) return "#00ff00";
            else if(monto<200) return "#ffff00";
            else return "#ff3333";
        }

    },
    vuetify: new Vuetify()
});