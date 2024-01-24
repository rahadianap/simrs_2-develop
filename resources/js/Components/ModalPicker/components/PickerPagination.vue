<template>

    <div v-if="pagination.total_data > pagination.per_page || pagination.total_data > 10 " class="hims-table-pagination uk-grid-small" uk-grid style="padding:0;margin:0;">
        <div class="uk-width-expand@m uk-align-center@l" style="margin:0 auto;padding:0.5em;">
            <a href="#" class="uk-hidden@m" @click.prevent="prevIndexClick"  title="Halaman sebelumnya">Sebelumnya</a>
            <a href="#" class="uk-visible@m" uk-icon="icon: chevron-left;ratio:0.8" @click.prevent="prevIndexClick"  title="Halaman sebelumnya"></a>
            <a href="#" class="uk-visible@m" uk-icon="icon: chevron-double-left;ratio:1.2" @click.prevent="firstPageIndexClick"  title="Halaman pertama"></a>
            
            <span style="padding:0 0.5em 0 0.5em;">
                <span style="color:black;font-size:12px;">Hal : </span>
                <select class="uk-select uk-form-small" @change="selectPage" style="font-size:12px;max-width:80px;color:black;border-radius:5px; border:1px solid #cc0202;" v-model="selectedIndex">                
                    <option v-for="item in pagination.num_pages" :value="item">{{item}}</option>
                </select>    
            </span>
            <span class="uk-hidden@m" style="margin:0 1em 0 1em;">|</span>
            <a href="#" class="uk-visible@m" uk-icon="icon: chevron-double-right;ratio:1.2" @click.prevent="lastPageIndexClick" title="Halaman terakhir"></a>
            <a href="#" class="uk-visible@m" uk-icon="icon: chevron-right;ratio:0.8" @click.prevent="nextIndexClick"  title="Halaman selanjutnya"></a>
            <a href="#" class="uk-hidden@m" @click.prevent="nextIndexClick"  title="Halaman selanjutnya">Berikutnya</a>
        </div>
        <div class="uk-width-auto@m" style="margin:auto 0;padding:0.5em;">
            <span style="margin:0 auto;color:black;font-size:12px;">per hal. </span>
            <select class="uk-select uk-form-small" @change="numRowsChange" style="font-size:12px;width:100px;color:black;border-radius:5px;" v-model="per_page">                
                <option value="10">10 data</option>
                <option value="20">20 data</option>
                <option value="30">30 data</option>
                <option value="50">50 data</option>
            </select>            
        </div>   
        <div class="hims-table-info uk-width-auto@m">
            total data : <span>{{pagination.total_data}}</span> | hal <span>{{pagination.current_page}}</span> dari <span>{{pagination.num_pages}}</span>
        </div>
    </div>

</template>

<script>
export default {
    name : 'picker-pagination',
    props : {
        pagination : { 
            type:Object, 
            required:true, 
            default: { current_page :0, per_page:10, total_data:0, num_pages:0,  } 
        },
    },
    data() {
        return {
            selectedIndex : 1,
            maxPageIndex : 10,
            per_page : 10,
            pages : [],  
         }         
    },
    watch : {
        'pagination.current_page'(newVal, oldVal) {   
            this.createPagination();
        }
    },
    
    mounted() {       
        this.createPagination();
    },

    methods : {
        selectPage() {
            this.$emit('pageChange',this.selectedIndex);
        },
        //icon next page clicked
        nextIndexClick(){
            if(this.pagination.current_page != this.pagination.num_pages) {
                let index = this.pagination.current_page + 1;
                this.$emit('pageChange',index); 
            }
        },
        //icon previous page clicked
        prevIndexClick(){
            if(this.pagination.current_page > 1) {
                let index = this.pagination.current_page - 1;
                this.$emit('pageChange',index); 
            }
        },
        //icon first page clicked
        firstPageIndexClick(){
            this.$emit('pageChange',1); 
        },
        //icon last page clicked
        lastPageIndexClick(){
            this.$emit('pageChange',this.pagination.num_pages); 
        },

        setActiveIndex(index){
            if(index != this.pagination.current_page && index !== '...' ) {
                this.$emit('pageChange',index); 
            }
        },

        /**event numRowsChange */
        numRowsChange(){
            this.$emit('numRowsChange',this.per_page); 
        },

        createPagination() {  
            this.selectedIndex = this.pagination.current_page;
            
            this.pages = [];
            /**jika jumlah halaman kurang dari sama dengan 10 */
            if(this.pagination.num_pages <= this.maxPageIndex && this.pagination.num_pages > 1) {
                let i = 1;
                while(i <= this.pagination.num_pages) {
                    this.pages.push(i);
                    i++;
                }
            } 

            /**jika jumlah halaman lebih dari 10 */
            else if(this.pagination.num_pages > this.maxPageIndex ) {
                //jika current page masih dibawah maksimal index yang diijinkan
                if(this.pagination.current_page < this.maxPageIndex) {
                    let i = 1; 
                    while(i <= this.maxPageIndex) { 
                        this.pages.push(i); 
                        i++; 
                    }
                    /**jika index terakhir masih tidak sama dengan index terakhir */
                    if(i < this.pagination.num_pages) { 
                        this.pages.push('...'); 
                    }
                }
                //jika current page sudah melewati index maksimal yang boleh ditampilkan
                else { 
                    if(this.pagination.current_page > 1) {
                        this.pages.push('...');
                    }

                    let lastIndex = this.pagination.num_pages; 
                    
                    if(this.pagination.current_page < this.pagination.num_pages) {
                        lastIndex = this.pagination.current_page + 1;                        
                    }
                    
                    let startIndex = (this.pagination.current_page + 2) - this.maxPageIndex;
                    if(this.pagination.current_page == this.pagination.num_pages) {
                        startIndex = (this.pagination.num_pages + 1) - this.maxPageIndex;
                    }
                    //console.log(`start : ${startIndex}`);
                    // if(startIndex > 2) {
                    //     //this.pages.push('...');
                    // }
                    // if(startIndex <= 2) {
                    //     this.startIndex = this.pagination.num_pages - this.maxPageIndex;
                    // }

                    let i = startIndex;
                    while(i < lastIndex + 1) {
                        this.pages.push(i);
                        i++;
                    }

                    if(i <= this.pagination.num_pages) {
                        this.pages.push('...');
                    }
                    //this.pages.push(this.pagination.num_pages);
                }
                
                
            }
        }
    }
}
</script>
