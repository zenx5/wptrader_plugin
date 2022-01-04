class WPTrader {
    constructor(fields, users, actions, investments, rates){
        this.fields = fields;
        this.users = users;
        this.actions = actions;
        this.rates = rates;
        this.investments = investments;
        this.investments.forEach( 
            ( element, index ) => 
            {
                this.investments[index].monto = parseFloat( element.monto )
            }
        );
    }

    setSettings(settings) {
        this.settings = settings;
    }
    getUser(id) {
        return this.users.filter( user => user.id == id );
    }

    getInvestments(id){
        return this.investments.filter( 
            investment =>
            {
                return investment.usuario == id;
            }
        )
    }

    getRates(mount) {
        return this.rates.filter( rate => {
            if( ( rate.mountdown <= mount ) && ( rate.mountup >= mount ) ){
                return rate;
            }
        } );
    }

    fields(){
        return this.fields;
    }

    getMountAll( id ){
        let investments = this.investments.filter( investment => investment.usuario == id );
        let total = 0;
        investments.forEach( 
            investment => 
            {
                total += investment.monto
            }
        )
        return total;

    }
    
}