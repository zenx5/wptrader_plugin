console.log($t);

let app = new Vue({
    el: "#wp-trader-app",
    data() {
        return {
            render: false,
            tab: 0,
            settings: {
                tiempoCobro: 180,
                rmin: 30,
                actionMax: 100
            },            
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
            newInvestment: {
                usuario: -1, 
                fecha: '',
                monto: 0,
                released: false
            },
            currentActions: 0,
            newActions: {
                id: -1,
                precio: 0,
                foot: 0,
                head: 0
            },
            investments: [],
            headerSetting:[
                { text: "Color" , value: "color", align: "center" },
                { text: "Rate (%)" , value: "rate", align: "center" },
                { text: "Inversion Mínima ($)" , value: "investmin", align: "center" },
                { text: "Inversion Máxima ($)" , value: "investmax", align: "center" },
                { text: "Accion" , value: "action", align: "center" }
            ],
            headerAction:[
                { text: "Precio de la accion" , value: "precio", align: "center" },
                { text: "Numero de acciones minimo" , value: "foot", align: "center" },
                { text: "Numero de acciones maximo" , value: "head", align: "center" },
                { text: "Accion" , value: "action", align: "center" }
            ],
            actions: [],
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
                wpid: -1,
                count: null,
                actions: []
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
        this.actions = $t.actions;
        this.investments = $t.investments;
        this.rates.unshift(this.newRate)
        console.log( this.actions )
        this.actions.unshift(this.newActions)
        this.settings = $t.settings[0];
        await this.getData();
    },
    filters: {
        totalActions: (elements, option) => {
            if(elements == undefined) return 0;
            let value = 0;
            elements.forEach( element => {
                if(option == 'cantidad') {
                    value += element.cantidad;
                }else if(option=='valor'){
                    value += element.cantidad*element.precio;
                }
            })
            return value;
        },
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
        notUsed: (elements, users) => {
            return elements.filter( element => users.filter( user => element.ID==user.wpid).length==0)
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
        validateActionRange(){
            let foot = parseInt( this.newActions.foot ),
                head = parseInt( this.newActions.head ),
                valid = false;
            
            this.actions.forEach( (action, index) => {
                if( index != 0 ) {
                    if(( foot >= parseInt(action.foot) )&&( foot <= parseInt(action.head) )) {valid = true;}
                    if(( head >= parseInt(action.foot) )&&( head <= parseInt(action.head) )) {valid = true;}
                }
            })
            return valid;
        },
        validateRateRange(){
            let min = parseFloat( this.newRate.investmin ),
                max = parseFloat( this.newRate.investmax ),
                valid = false;
            
            this.rates.forEach( (rate, index) => {
                if( index != 0 ) {
                    if(( min >= parseFloat(rate.investmin) )&&( min <= parseFloat(rate.investmax) )) {valid = true;}
                    if(( max >= parseFloat(rate.investmin) )&&( max <= parseFloat(rate.investmax) )) {valid = true;}
                }
            })
            return valid;
        },
        validated( type ) {
            let valid = true;
            switch( type ){
                case 'wpt_users':

                    valid = valid && ( !!this.temp.nombre );
                    valid = valid && ( !!this.temp.apellido );
                    valid = valid && ( !!this.temp.cedula );
                    valid = valid && ( !!this.temp.correo );
                    valid = valid && ( this.temp.correo.indexOf('@') != -1 );
                    valid = valid && ( !!this.temp.pais );
                    valid = valid && ( !!this.temp.postalcode );
                    valid = valid && ( !!this.temp.telefono );
                    valid = valid && ( !!this.temp.wpid );
                    break;
            }
            return valid;
        },
        reset() {
            this.settings.rmin = 30;
            this.settings.tiempoCobro = 180;
            this.settings.actionMax = 100;

        },
        cobrar( item ){
            item.released = true;
            this.newInvestment = item;
            this.save('wpt_investments', item.id )
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
                this.getAjax("count_down", {id:index})
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
                    wpid: -1,
                    actions: []
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
                        wpid: -1,
                        actions: []
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
                case 'wpt_actions': 
                    if( id != -1 ){
                        let index = -1;
                        this.actions.forEach( 
                            (action, $index) => 
                            {
                                if( action.id == id ) index = $index;
                            }
                        );
                        this.actions[index] = data;
                    }else{
                        this.actions.push( data );
                    }
                    this.newActions = {
                        id: -1,
                        precio: 0,
                        foot: 0,
                        head: 0
                    }
                    break;
                case 'wpt_investments':
                    this.investments.push( data )
                    this.newInvestment = {
                        usuario: -1, 
                        fecha: '',
                        monto: 0,
                        released: false
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
                case 'wpt_actions': 
                    this.actions.forEach( 
                        action => 
                        {
                            if( action.id > max ) max = action.id;
                        }    
                    );
                    this.newActions.id = max + 1;
                    return this.newActions;
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
                else if( type == 'wpt_settings' ) { dataSend.append('value', JSON.stringify( this.settings ) ); }
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
                else if( type == 'wpt_actions' ){
                    this.actions = this.actions.filter( action => action.id != $index );
                    if( this.actions.length == 0){
                        this.actions.push({
                            id: -1,
                            precio: 0,
                            foot: 0,
                            head: 0
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
                actions: []
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
        },
        async getAjax( f, $data ) {
            let dataSend = new FormData();
            dataSend.append('action', 'wpt_get_data_for_ajax');
            dataSend.append('f', f );
            dataSend.append('data', JSON.stringify( $data ) );
            let { data } = await axios.post(ajaxurl, dataSend);
            this.temp.count = data;
            this.render = !! this.render;
        },
        nextPay(id){
            let max = 0;
            const {fechaCobro} = this.$options.filters;
            fechaCobro( this.investments.filter( investment => investment.usuario == id), this.settings.tiempoCobro )
            .forEach( ivestment => {
                if( ivestment.cobro > max ){
                    max = ivestment.cobro;
                }
            })
            return max;
        },
        async saveWithWP(){
            let dataSend = new FormData();
            dataSend.append('action', 'wpt_save_data_with_wp');
            dataSend.append('id', this.temp.wpid );
            console.log( this.temp.wpid );
            let { data } = await axios.post(ajaxurl, dataSend);
            console.log( data );
            if( data ) {
                console.log()
                this.users.push( data );
            }
        },
        setAction(){
            let typeAction = this.actions.filter( action => {
                if( ( action.foot <= this.currentActions ) && ( this.currentActions <= action.head ) ) return action;
            });
            console.log( this.details, this.users[ this.details ] )
            this.users[ this.details ].actions.push({
                precio: parseFloat( typeAction[0].precio ),
                cantidad: parseInt( this.currentActions )
            });
            
        }
        
    },
    vuetify: new Vuetify()
});
