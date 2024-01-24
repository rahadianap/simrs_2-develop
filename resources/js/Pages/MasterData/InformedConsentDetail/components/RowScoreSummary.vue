<template>
    <tr style="border-bottom:1px solid #eee;background-color:#fafafa;">
        <td v-if="isExpandable" style="color:black;width:20px;text-align:center;padding:0.5em 0.2em 0.5em 0.2em;margin:0;">
            <a v-if="isExpanded" href="#" uk-icon="icon:chevron-up;ratio:0.7;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:chevron-down;ratio:0.7;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td colspan="2" style="color:black;padding:0.5em 0.2em 0.5em 0.2em; margin:0;">                                                        
            <span style="font-weight: 500;margin-right:5px;">{{ dt.variable }}</span>
            <a href="#" @click.prevent="removeItemCallback(dt)" style="padding:0.2em;line-height: 11px; display:inline-block; border:none; background-color: #fff;font-size:11px; font-style: italic;">hapus</a>
        </td>
    </tr>             
    <tr v-if="isExpandable">
        <Transition>
            <!-- <td></td> -->
            <td colspan="3" style="padding:0;margin:0;" v-if="isExpanded">
                <table class="uk-table">
                    <thead>
                        <tr style="margin:0;padding:0.2em;border-bottom:1px solid #ccc;">      
                            <th style="font-size:10px;margin:0;padding:0.5em;color:black;font-weight:500;">ITEM</th>
                            <th style="font-size:10px;margin:0;padding:0.5em;color:black;font-weight:500;width:50px;text-align:center;">VALUE</th>
                            <th style="font-size:10px;margin:0;padding:0.5em;color:black;font-weight:500;width:50px;text-align:center;">DEFAULT</th>
                            <th style="font-size:10px;margin:0;padding:0.5em;color:black;font-weight:500;width:40px;text-align:center;">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in dt.item_score" style="border-bottom:1px solid #eee;">                                                                    
                            <td style="color:black;padding:0.2em;margin:0;">
                                <!-- <input class="uk-input uk-form-small" type="text" placeholder="item" v-model="item.item_score" style="font-weight: 500;font-size:14px;border:none;"> -->
                                <p  style="font-weight: 400; font-style:italic; font-size:12px;padding:0.2em;">{{ item.item_score }}</p>
                            </td>
                            <td style="color:black;width:50px;text-align:center;padding:0.2em;margin:0;">
                                <input class="uk-input uk-form-small" type="text" placeholder="nilai" v-model="item.score" style="text-align: center;">
                            </td> 
                                <td style="color:black;width:50px;text-align:center;padding:0.4em 0.2em 0.5em 0.2em;margin:0;">                                                    
                                <input class="uk-checkbox" type="checkbox" v-model="item.is_default">
                            </td>
                            <td style="color:black;width:40px;text-align:center;padding:0.3em;margin:0;">
                                <button type="button" @click.prevent="removeScoreItem(item)" style="padding:0.2em; border:none; background-color: #fff;"><span uk-icon="icon:trash"></span></button>
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #eee;">                                                                    
                            <td style="color:black;padding:0.2em;margin:0;">
                                <input class="uk-input uk-form-small" type="text" placeholder="item" v-model="itemScore.item_score" style="font-weight: 500;font-size:14px;">
                            </td>
                            <td style="color:black;width:50px;text-align:center;padding:0.2em;margin:0;">
                                <input class="uk-input uk-form-small" type="text" placeholder="nilai" v-model="itemScore.score" style="text-align: center;">
                            </td> 
                                <td style="color:black;width:50px;text-align:center;padding:0.4em 0.2em 0.5em 0.2em;margin:0;">                                                    
                                <input class="uk-checkbox" type="checkbox" v-model="itemScore.is_default">
                            </td>
                            <td style="color:black;width:40px;text-align:center;padding:0.4em;margin:0;">
                                <button type="button" @click.prevent="addScoreItem" style="padding:0.2em; border:none; background-color: #fff;"><span uk-icon="icon:plus-circle"></span></button>
                            </td>
                        </tr>     
                    </tbody>
                </table>
            </td>
        </Transition>
        
    </tr>
</template>

<script>

export default {
    props : {
        dt : { type:Object, required:false, default:null },
        removeItemCallback : { type:Object, required:false, default:null },
    },
    name : 'row-score-summary',
    data() {
        return {
            isExpanded : false,
            isExpandable : true,
            buffer : [],
            itemScore : {
                item_score : null,
                score : null,
                value : null,
                is_default : false,    
            }
        }
    },
    methods : {
        changeRowExpand() {
            this.isExpanded = !this.isExpanded;
            return false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString(); 
            return val;
        },

        addScoreItem(){
            console.log(this.dt);

            let val = JSON.parse(JSON.stringify(this.itemScore));
            this.dt.item_score.push(val);
            this.clearItemScore();
        },

        clearItemScore(){
            this.itemScore.item_score = null;
            this.itemScore.score = null;
            this.itemScore.value = null;
            this.itemScore.is_default = false;    
        },

        removeScoreItem(item) {

        }
    }
}

</script>

