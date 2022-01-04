console.log($t);

let app = new Vue({
    el: "#wp-trader-app",
    data() {
        return {
            render: false,
            tab: 0,
            tiempoCobro: 180,
            rmin: 30,
            details: -1,
            editRow: -1,
            tabs: ["Dashboard", "Settings", "Details"],
            title: "Title of the Dashboard",
            headerCountrie: [
                { text: "Label" , value: "label", align: "center" },
                { text: "Enable" , value: "enable", align: "center" },
            ],
            headerInvestment: [
                { text: "Fecha de Inicio" , value: "fecha", align: "center" },
                { text: "Fecha de Cobro" , value: "fechacobro", align: "center" },
                { text: "Monto ($)" , value: "monto", align: "center" },
                { text: "Dias para cobrar" , value: "cobro", align: "center" },
                { text: "Accion" , value: "action", align: "center" }
            ],
            newInvestment: {},
            investments: [],
            headerSetting:[
                { text: "Color" , value: "color", align: "center" },
                { text: "Rate (%)" , value: "rate", align: "center" },
                { text: "Inversion Mínima ($)" , value: "investmin", align: "center" },
                { text: "Inversion Máxima ($)" , value: "investmax", align: "center" },
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
                id: -1,
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
                monto: 0,
                wpid: -1
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
                if( country.enable == undefined ) {
                    this.countries[$index].enable = true;
                }
            }
        )

        localStorage.setItem("wpt_countries", JSON.stringify( this.countries ) ) 
        
        this.users = $t.users;
        this.rates = $t.rates;
        this.investments = $t.investments;
        this.rates.unshift(this.newRate)
        this.rmin = $t.settings.rmin;
        this.tiempoCobro = $t.settings.tiempoCobro;
        this.countrySelect = $t.settings.countrySelect;
        await this.getData();
    },
    filters: {
        date: value => {
            if( value ) {
                let valueArray = value.split("-");
                if( valueArray[0].length > 2 ){
                    aux = valueArray[0];
                    valueArray[0] = parseInt( valueArray[1] ) - 1;
                    valueArray[1] = valueArray[2];
                    valueArray[2] = aux;
                }
                return [
                    "Enero", "Febrero", "Marzo",
                    "Abril", "Mayo", "Junio",
                    "julio", "Agosto", "Septiembre",
                    "Octubre", "Noviembre", "Diciembre",
                ][ parseInt( valueArray[0] ) ]+", "+valueArray[1]+" del "+valueArray[2];
            }
        }, 
        forKey: (elements, key, id) => {
            return elements.filter( element => element[ key ] == id );
        },
        fechaCobro: (elements,tiempo) => {
            return elements.filter( (element, index) => {
                let hoyms = (new Date()).getTime();
                let fechams = (new Date( element.fecha )).getTime();
                let fechacobroms = fechams + tiempo*1000*60*60*24;
                let fechacobro = new Date( fechacobroms );
                elements[ index ].fechacobro = (fechacobro.getMonth() + 1)+"-"+fechacobro.getDate()+"-"+fechacobro.getFullYear();
                elements[ index ].cobro = parseInt( ( fechacobroms - hoyms )/1000/60/60/24 );
                return true;
            })
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
                this.temp = {
                    id: -1,
                    nombre: "",
                    apellido: "",
                    cedula: "",
                    correo: "",
                    pais: "",
                    postalcode: "",
                    telefono: "",
                    monto: 0,
                    wpid: -1
                }; 
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
                    this.temp = {
                        id: -1,
                        nombre: "",
                        apellido: "",
                        cedula: "",
                        correo: "",
                        pais: "",
                        postalcode: "",
                        telefono: "",
                        monto: 0,
                        wpid: -1
                    };
                    break;
                case 'wpt_rates': 
                    if( id != -1 ){
                        let index = -1;
                        this.rates.forEach( 
                            (rate, $index) => 
                            {
                                if( rate.id == id ) index = $index;
                            }
                        );
                        this.rate[index] = data;
                    }else{
                        this.rates.push( data );
                    }
                    this.newRate = {
                        id: -1,
                        color: "#fff",
                        rate: 0,
                        investmin: 0,
                        investmax: 0
                    }
                    break;
                case 'wpt_investments':
                    this.investments.push( data )
                    this.newInvestment = {
                        fecha: '',
                        monto: 0
                    }
            }
        },
        content( type ){
            let max = 0;
            switch( type ) {
                case 'wpt_users': 
                    this.users.forEach( 
                        user => 
                        {
                            if( user.id > max ) max = user.id;
                        }    
                    );
                    this.temp.id = max + 1;
                    return this.temp;
                case 'wpt_rates': 
                    this.rates.forEach( 
                        rate => 
                        {
                            if( rate.id > max ) max = rate.id;
                        }    
                    );
                    this.newRate.id = max + 1;
                    return this.newRate;
                case 'wpt_investments':
                    this.newInvestment.usuario = this.temp.id;
                    this.investments.forEach(
                        investment => 
                        {
                            if( investment.id > max ) max = investment.id;
                        }
                    );
                    this.newInvestment.id = max + 1;
                    return this.newInvestment;

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
                if( type == 'wpt_users') { dataSend.append('value', JSON.stringify( this.users.filter( user => user.id == $index )[0] ) ); }
                else if( type == 'wpt_rates' ) { dataSend.append('value', JSON.stringify( this.rate.filter( rate => rate.id == $index )[0] ) ); }
                //else { dataSend.append('value', JSON.stringify( this.investments.filter( investment => investment.id == $index )[0] ) ); }
            }
            const { data } = await axios.post(ajaxurl, dataSend);
            if( data ) {
                this.addContent( type, data, $index );
            }
            this.editRow = -1;
            
            
            this.render = ! this.render;
        },
        async del(type, $index) {
            let dataSend = new FormData();
            dataSend.append('action', 'wpt_delete_data');
            dataSend.append('target', type);
            dataSend.append('index', $index);
            const { data } = await axios.post(ajaxurl, dataSend);
            if( data ) {
                if( type == 'wpt_users' ) {
                    this.users = this.users.filter( user => user.id != $index );
                }else if( type == 'wpt_rates' ){
                    this.rates = this.rates.filter( rate => rate.id != $index );
                    if( this.rates.length == 0){
                        this.rates.push({
                            id: -1,
                            color: "#fff",
                            rate: 0,
                            investmin: 0,
                            investmax: 0
                        });
                    }                    
                }
                else if ( type == 'wpt_investments' ) {
                    console.log(this.investments,$index)
                    this.investments = this.investments.filter( investment => investment.id != $index );
                }
            }
            this.temp = {
                id: -1,
                nombre: "",
                apellido: "",
                cedula: "",
                correo: "",
                pais: "",
                postalcode: "",
                telefono: "",
                monto: 0,
                wpid: -1,
            };
            this.newRate = {
                id: -1,
                color: "#fff",
                rate: 0,
                investmin: 0,
                investmax: 0
            }
            this.render = ! this.render;
        },
        getColor(monto){
            let color = "#fff";
            this.rates.forEach( 
                rate => 
                {
                    if( (monto >= rate.investmin) && (monto < rate.investmax) ) {
                        color = rate.color;
                    }
                }    
            );
            return color;
        }

    },
    vuetify: new Vuetify()
});