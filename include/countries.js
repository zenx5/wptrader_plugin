(
  _ =>
  {
    if( ! localStorage.getItem("wpt_countries") ){
        axios.get("https://restcountries.com/v3.1/all").then(
            response => 
            {
                response.data.forEach( 
                  (country, index) => 
                  {
                    response.data[ index ].label = country.name.common;
                    response.data[ index ].enable = true;
                  }
                )
                localStorage.setItem("wpt_countries", JSON.stringify(response.data) )
            }
        )
    }
  }  
)();