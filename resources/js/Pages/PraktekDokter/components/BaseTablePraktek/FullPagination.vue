<template>
    <div v-bind="pagination.total_data > pagination.per_page || pagination.total_data > 10 " class="hims-table-pagination uk-grid-small uk-width-1-1" uk-grid style="padding:0;margin:0;">
        
        <div class="uk-grid-column-small uk-grid-row-large uk-grid uk-child-width-expand@s uk-text-center uk-first-column uk-width-1-1" style="margin:0 auto;padding:0.5em;">
            <div class="uk-width-auto@s" style="margin-left: 20px;">   
                 <a href="#" class="uk-hidden@s" @click.prevent="prevIndexClick"  title="Halaman sebelumnya">Sebelumnya</a>
                <a href="#" class="uk-visible@s first-last-page-button uk-padding-small uk-align-center uk-align-left@s" @click.prevent="firstPageIndexClick"><span uk-icon="icon: chevron-double-left;ratio:1.2"></span>Halaman pertama</a>
            </div>

            <div class="uk-width-expand@s">
                <span v-if="mode == 'SIMPLE'" class="uk-flex uk-flex-center" style="padding:0 0.5em 0 0.5em;gap:10px">
                    <a href="#" class="uk-visible@s uk-margin-right" uk-icon="icon: chevron-left;ratio:0.8" style="border-radius:100%" @click.prevent="prevIndexClick"  title="Halaman sebelumnya"></a>
               
                    <!-- nomor halaman -->
                    <div class="uk-flex-center uk-flex uk-padding-small hims-pagination-number" v-for="pageNumber in pagination.num_pages" :key="pageNumber">
                       <span> 
                            <a href="#" 
                                @click.prevent="setActiveIndex(pageNumber)" 
                                :class="{ active: pageNumber === pagination.current_page }"
                                style="border-radius: 100%;"
                            >{{ pageNumber }}
                            </a> 
                        </span>
                    </div>
                    <!-- END nomor halaman -->

                    <a href="#" class="uk-visible@s uk-margin-left" uk-icon="icon: chevron-right;ratio:0.8" style="border-radius:100%" @click.prevent="nextIndexClick"  title="Halaman selanjutnya"></a>  
                </span>

                <span v-else>
                    <a v-for="pageNumber in pages" v-bind:class="{ 'active': pageNumber == pagination.current_page, 'uk-visible@m' :true }" href="#" @click.prevent="setActiveIndex(pageNumber)">{{pageNumber}}</a>            
                </span>
            </div>

            <div class="uk-width-auto@s" style="margin-right: 40px;">
                <span class="uk-hidden@s" style="margin:0 1em 0 1em;">|</span>
                <a href="#" class="uk-visible@s first-last-page-button uk-align-right@s" @click.prevent="lastPageIndexClick"><span uk-icon="icon: chevron-double-right;ratio:1.2">Halaman terakhir</span></a>
                <a href="#" class="uk-hidden@s" @click.prevent="nextIndexClick"  title="Halaman selanjutnya">Berikutnya</a>
            </div>
        </div>

        <div class="uk-width-1-2 uk-flex uk-flex-center uk-align-center uk-grid-margin uk-first-column">
            <div style="margin:auto 0;padding:0.5em;">
                <span style="margin:0 auto;color:black;font-size:12px;">per hal. </span>
                <select class="uk-select uk-form-small" @change="numRowsChange" style="font-size:12px;width:100px;color:black;border-radius:5px;" v-model="per_page">                
                    <option value="10">10 data</option>
                    <option value="20">20 data</option>
                    <option value="30">30 data</option>
                    <option value="50">50 data</option>
                    <option value="100">100 data</option>
                    <option value="ALL">Semua</option>
                </select>            
            </div>   
            <div class="hims-table-info">
                total data: <span>{{ pagination.total_data }}</span> | hal <span>{{ pagination.current_page }}</span> dari <span>{{ pagination.num_pages }}</span>
            </div>
        </div>
    </div>

</template>

<script>
    import '@/Pages/PraktekDokter/Dashboard/tabs/dashboardStyle.css';

    export default {
        name : 'full-pagination',
        props : {
            mode : { type:String, required:false , default: 'SIMPLE' },
            pagination : { 
                type:Object, 
                required:true, 
                default: { current_page :1, per_page:10, total_data:0, num_pages:0,  } 
            },
        },
        data() {
            return {
                selectedIndex: this.pagination.current_page,
                maxPageIndex : 10,
                per_page: this.pagination.per_page,
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
            //  select page number indexing
            selectPage() {
                this.$emit('pageChange', this.selectedIndex);
            },
   
            // icon next page clicked
            nextIndexClick() {
                if (this.pagination.current_page < this.pagination.num_pages) {
                    const index = this.pagination.current_page + 1;
                    this.$emit('pageChange', index);
                }
            },

            //icon previous page clicked
            prevIndexClick() {
                if (this.pagination.current_page > 1) {
                    const index = this.pagination.current_page - 1;
                    this.$emit('pageChange', index);
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
                this.pagination.num_pages = Math.ceil(this.pagination.total_data / this.pagination.per_page);
                this.pages = [];

                if (this.pagination.num_pages > 1) {
                for (let i = 1; i <= this.pagination.num_pages; i++) {
                    this.pages.push(i);
                }
            }

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
<style>

/**table pagination */

/* Jika settingan display laptop Anda:
    - Scale: 200%
    - Resolution: 2560 x 1440
*/
/* ipad mini */
@media  screen and (max-width: 780px) {
    /* .uk-width-auto\@m {
        width: auto;
    } */
    .pagination-flex {
        flex: 1 0 calc(100% - 31px);
    } 
}
.hims-table-margin {
    margin: 30px 0px -30px 0px;
}
.hims-table-pagination {
    padding : 0 0.5em 0 0;
    margin:0;
    text-align : left;
    border-bottom-right-radius: 5px;
    border-bottom-left-radius: 5px;
}

.hims-table-pagination a {
    display: inline-block;
    margin:0 0 0 0;
    padding:0;
    color:#666;
    font-size:14px;
    line-height:40px;
    min-width:40px;
    text-align:center;
    text-decoration: none;
    border: 2px solid #aba7a7;
    color:black;
}
.hims-pagination-number {
    border: 1px solid #aba7a7;
    background: rgba(217, 217, 217, 0.2);
    border-radius: 100%;
    color:black;
    width:10px;
    height:10px;
    align-items:center;
    text-align: center;
    cursor:pointer;
}
.hims-pagination-number:hover {
    background: #cc0202;
    color: white;
    font-weight:bold;

}
.hims-table-pagination a:hover {
    background-color: #cc0202;
    border: 2px solid #cc0202;
    color: white;
    font-weight:bold;
}

.hims-table-pagination a.active {
    background-color: #cc0202;
    border: 2px solid #cc0202;
    color: white;
    font-weight:bold;
}
.hims-table-pagination .hims-table-info {
    margin:auto 0;
    padding:1em;
    font-size:12px;
    color:#666;
}
.hims-table-pagination .hims-table-info span {
    font-weight:500;
    color:black;
}
.first-last-page-button {
    display:block;
    justify-content: center;
    align-items:center;
    border: 1px solid rgba(0, 0, 0, 0.7);
    background: rgba(217, 217, 217, 1);
    border-radius: 12px;
    width: 150px;
    height: auto;
    flex-shrink: 0;
}
</style>
