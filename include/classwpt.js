class WPTrader {
    constructor(fields, users, investments, rates){
        this.fields = fields;
        this.users = users;
        this.rates = rates;
        this.investments = investments;
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