<template>
	<div>

    <div class='container text-center space-outside-md' v-if="loading">
        <i class='fa fa-cog fa-spin fa-5x fa-fw text-color-accent'></i>
        <p class='text-color-accent space-outside-md font-md '>Searching</p>
    </div>


    <div class="fixed bg-main space-inside-sm space-inside-sides-md text-center">

        <a class='text-color-accent space-inside-sides-md transition-normal text-hover-light font-md' href='#' id='maps'>Maps</a>
        <a class='text-color-accent space-inside-sides-md transition-normal text-hover-light font-md' href='#' id='maps'>Hotels</a>
        <a class='text-color-accent space-inside-sides-md transition-normal text-hover-light font-md' href='#' id='maps'>Temperature</a>

    </div>

    <div class="jumbotron animated fadeInLeft wow" v-if="foundResults">
        <div class="container">

            <div class='right'>
                <p  v-text="weather.temperature()" style="display: inline;"></p>

                C

                <img :src="weather.icon()" style="display: inline; width: 70px;" />
            </div>

            <div class='left'>

                <h2 v-text="weather.cityName()" style="display: inline;"></h2>, <h2 v-text="weather.country()" style="display: inline;"></h2>

                <p class='text-color-accent space-outside-xs' v-text="weather.weatherType()"></p>

                <p class='space-outside-xs' v-text="weather.dressingAdvice()"></p>

            </div>

        </div>

        <hr class="hr-orange">

        <div id="hotel-results"></div>
    </div>


</template>

<script>

    export default {

        data(){
            return{
                searchResults: "",
                loading: false,
                weather: null,
                foundResults: false,
            }
        },

        mounted() {
            console.log('Component mounted.')
        },

        created(){

            Event.listen('searching', () => {
               this.loading = true;
            });


            Event.listen('weatherFound', (weather) => {
                this.loading = false;
                this.weather = weather;
                this.foundResults = true;
            });


        	Event.listen('searchResultsFound', (searchResults) => {
               this.loading = false;
			   this.setSearchResults(searchResults);
			});
        },


        methods:
        {
	    	/**
			 * setter for searchResults
			 * @param {[type]} searchResults [description]
			 */
			setSearchResults(searchResults) {
			   this.searchResults = searchResults;
			},
        }
    }




</script>
