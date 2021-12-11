console.log($t);

let app = new Vue({
    el: "#wp-trader-app",
    data() {
        return {
            render: false,
            tab: 0,
            editRow: -1,
            tabs: ["Dashboard", "Settings","About"],
            title: "Title of the Dashboard",
            headers: [
                { text: "Accion" , value: "accion", align: "center"}
            ],
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
        async getData(){
            
            this.users.forEach( 
                (user, index) => {
                    this.users[ index ].monto = $t.getMountAll( user.id )
                    this.users[ index ].edit = false;
                }
            )
        },
        view() {
            console.log("view");
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
        }

    },
    vuetify: new Vuetify()
});