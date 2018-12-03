<template>
    <div class="panel-heading">
    <!-- 搜索用户 -->
        <div class="form-horizontal">
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">名称：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.title" id="search-input-name" placeholder="请输入名称">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">期权标的：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.token_type" id="search-input-name" placeholder="请输入期权标的">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">合约类型：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control"  v-model="data.contract_type" id="search-input-name" placeholder="请输入合约类型">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">计价单位：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.unit_valuation" id="search-input-name" placeholder="请输入计价单位">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*发行价：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.issue_price" id="search-input-name" placeholder="请输入发行价">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">最小价格单位：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.min_price" id="search-input-name" placeholder="请输入最小价格单位">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*合约比例：</label>
                <div class="col-sm-8">
                    币种<input type="text" v-model="data.contract_first"> ：
                    合约<input type="text" v-model="data.constarct_second">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">文本描述：</label>
                <div class="col-sm-4">
                    <textarea class="form-control" style= "outline:none;resize:none;" v-model="data.description" max-length= "200" placeholder="文本描述非必填项"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">交割方式：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.delivery_type" id="search-input-name" placeholder="请输入交割方式">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*行权价格：</label>
                <div class="col-sm-4">
                    <input  type="text" v-model="data.exercise_price" placeholder="请输入行权价格"><span>RMB</span>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*上市时间：</label>
                <div class="col-sm-4">
                    <el-date-picker
                            v-model="data.ipo_time"
                            type="datetime"
                            format="yyyy-MM-dd HH:mm"
                            value-format="yyyy-MM-dd HH:mm"
                            placeholder="选择日期时间">
                    </el-date-picker>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*期权到期时间：</label>
                <div class="col-sm-4">
                    <el-date-picker
                            v-model="data.end_time"
                            type="datetime"
                            format="yyyy-MM-dd HH:mm"
                            value-format="yyyy-MM-dd HH:mm"
                            placeholder="选择日期时间">
                    </el-date-picker>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*交易期限：</label>
                <div class="col-sm-4">
                    <el-date-picker
                            v-model="data.pay_start_time"
                            type="datetime"
                            format="yyyy-MM-dd HH:mm"
                            value-format="yyyy-MM-dd HH:mm"
                            placeholder="选择日期时间">
                    </el-date-picker>
                到
                </div>
                <div class="col-sm-4">
                    <el-date-picker
                            v-model="data.pay_end_time"
                            type="datetime"
                            format="yyyy-MM-dd HH:mm"
                            value-format="yyyy-MM-dd HH:mm"
                            placeholder="选择日期时间">
                    </el-date-picker>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*行权时间：</label>
                <div class="col-sm-4">
                    <el-date-picker
                            v-model="data.exercise_start_time"
                            type="datetime"
                            format="yyyy-MM-dd HH:mm"
                            value-format="yyyy-MM-dd HH:mm"
                            placeholder="选择日期时间">
                    </el-date-picker>
                    到
                </div>
                <div class="col-sm-4">
                    <el-date-picker
                            v-model="data.exercise_end_time"
                            type="datetime"
                            format="yyyy-MM-dd HH:mm"
                            value-format="yyyy-MM-dd HH:mm"
                            placeholder="选择日期时间">
                    </el-date-picker>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*每人最多购买期权数量：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.max_num_option" id="search-input-name" placeholder="请输入每人最多购买期权数量">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">履行价格确认方式：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.execute_type" id="search-input-name" placeholder="请输入履行价格确认方式">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">履行价格确认平台：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.execute_platform" id="search-input-name" placeholder="请输入履行价格确认平台">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">履行价格权重比例：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.execute_weight_ratio" id="search-input-name" placeholder="请输入履行价格权重比例">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">保证金标的</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.bail_type" id="search-input-name" placeholder="请输入保证金标的">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">保证金率：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.bail_ratio" id="search-input-name" placeholder="请输入保证金率">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">最小数量单位：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.min_number" id="search-input-name" placeholder="请输入最小数量单位" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">保证金：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.bail" id="search-input-name" placeholder="请输入保证金">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">其他要求：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.charge_unit" id="search-input-name" placeholder="请输入其他要求">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">保证金返还：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" id="search-input-name" placeholder="请输入保证金返还">
                </div>
            </div>
        </div>
        <div class="form-group">带有'*'的为必填项</div>
        <div class="col-sm-offset-2 col-sm-1">
            <button class="btn btn-primary btn-sm" @click="release(2)" v-if="axios">
                发布
            </button>
            <button class="btn primary btn-sm" v-if="!axios">
                发布
            </button>
        </div>
        <div class="col-sm-offset-1 col-sm-1">
            <button class="btn btn-primary btn-sm" @click="release(3)" v-if="axios">
                存为草稿
            </button>
            <button class="btn primary btn-sm" v-if="!axios">
                存为草稿
            </button>
        </div>
         <div class="col-sm-offset-1 col-sm-1">
         <button class="btn btn-primary btn-sm" type="text" @click="dialogTableVisible = true">
         预览
         </button>
         <el-dialog title="发布信息" :visible.sync="dialogTableVisible" width="80%" >
             <el-form :model="data">
               <el-form-item label="名称" v-if="data.title" :label-width="formLabelWidth">
                 <el-input v-model="data.title" autocomplete="名称" disabled></el-input>
               </el-form-item>

               <el-form-item label="期权标的" v-if="data.token_type" :label-width="formLabelWidth">
                  <el-input v-model="data.token_type" autocomplete="期权标的" disabled></el-input>
               </el-form-item>

               <el-form-item label="合约类型" v-if="data.contract_type" :label-width="formLabelWidth">
                  <el-input v-model="data.contract_type" autocomplete="期权标的" disabled></el-input>
               </el-form-item>

               <el-form-item label="计价单位" v-if="data.unit_valuation" :label-width="formLabelWidth">
                    <el-input v-model="data.unit_valuation" autocomplete="期权标的" disabled></el-input>
               </el-form-item>

               <el-form-item label="发行价" v-if="data.issue_price" :label-width="formLabelWidth">
                    <el-input v-model="data.issue_price" autocomplete="期权标的" disabled></el-input>
               </el-form-item>

               <el-form-item label="最小价格单位" v-if="data.min_price" :label-width="formLabelWidth">
                    <el-input v-model="data.min_price" autocomplete="期权标的" disabled></el-input>
               </el-form-item>

               <el-form-item label="合约比例" v-if="data.contract_first&&data.constarct_second" :label-width="formLabelWidth">
                            币种<input v-model="data.contract_first" autocomplete="期权标的" disabled></input>
                        ：
                            合约<input v-model="data.constarct_second" widt autocomplete="期权标的" disabled></input>
                            文本描述<input type="text" v-model="data.description" placeholder="文本描述非必填项" disabled>
               </el-form-item>

               <el-form-item label="交割方式" v-if="data.delivery_type" :label-width="formLabelWidth">
                    <el-input v-model="data.delivery_type" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

               <el-form-item label="行权价格" v-if="data.exercise_price" :label-width="formLabelWidth">
                    <el-input v-model="data.exercise_price" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

               <el-form-item label="上市时间" v-if="data.ipo_time" :label-width="formLabelWidth">
                    <el-input v-model="data.ipo_time" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

               <el-form-item label="期权到期时间" v-if="data.end_time" :label-width="formLabelWidth">
                    <el-input v-model="data.end_time" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

               <el-form-item label="交易期限" v-if="data.pay_start_time&&data.pay_end_time" :label-width="formLabelWidth">
                    <input v-model="data.pay_start_time" autocomplete="期权标的" disabled></input>
                     ——
                    <input v-model="data.pay_end_time"widt autocomplete="期权标的" disabled></input>
               </el-form-item>
               <el-form-item label="行权时间" v-if="data.exercise_start_time&&data.exercise_end_time" :label-width="formLabelWidth">
                    <input v-model="data.exercise_start_time" autocomplete="期权标的" disabled></input>
                     ——
                    <input v-model="data.exercise_end_time" widt autocomplete="期权标的" disabled></input>
               </el-form-item>

               <el-form-item label="最多购买数量" v-if="data.max_num_option" :label-width="formLabelWidth">
                    <el-input v-model="data.max_num_option" autocomplete="交割方式" disabled></el-input>
               </el-form-item>
               <el-form-item label="履行价格确认方式" v-if="data.execute_type" :label-width="formLabelWidth">
                    <el-input v-model="data.execute_type" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

               <el-form-item label="履行价格确认平台" v-if="data.execute_platform" :label-width="formLabelWidth">
                    <el-input v-model="data.execute_platform" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

               <el-form-item label="履行价格权重比例" v-if="data.execute_weight_ratio" :label-width="formLabelWidth">
                    <el-input v-model="data.execute_weight_ratio" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

               <el-form-item label="保证金标的" v-if="data.bail_type" :label-width="formLabelWidth">
                    <el-input v-model="data.bail_type" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

               <el-form-item label="保证金率" v-if="data.bail_ratio" :label-width="formLabelWidth">
                    <el-input v-model="data.bail_ratio" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

               <el-form-item label="最小数量单位" v-if="data.min_number" :label-width="formLabelWidth">
                    <el-input v-model="data.min_number" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

               <el-form-item label="保证金" v-if="data.bail" :label-width="formLabelWidth">
                    <el-input v-model="data.bail" autocomplete="交割方式" disabled></el-input>
               </el-form-item>

                <el-form-item label="其他要求" v-if="data.charge_unit" :label-width="formLabelWidth">
                    <el-input v-model="data.charge_unit" autocomplete="交割方式" disabled></el-input>
               </el-form-item>
             </el-form>
           </el-dialog>
        </div>

    </div>
</template>
<style scoped>
.dropbtn {
  border: none;
  cursor: pointer;
}
.dropdown {
  position: relative;
  display: inline-block;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 40px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
}
.dropdown-content a {
  color: black;
  padding: 10px;
  text-decoration: none;
  display: block;
}
.dropdown-content a:hover {
  background-color: #f1f1f1;
}
.dropdown:hover .dropdown-content {
  display: block;
}
</style>


<script>
import lodash from "lodash";
import { mapGetters } from "vuex";
import request, { createRequestURI } from "../../util/request";
let moment = require("moment");
// import AreaLinkage from './AreaLinkage';

const AccountRelease = {
  /**
   * 定义当前组件状态数据
   *
   * @return {Object}
   * @author Seven Du <shiweidu@outlook.com>
   * @homepage http://medz.cn
   */
  data: () => ({
        data:{
            id:"",
            title:"",
            token_type:"",
            issue_price:"",
            exercise_start_time:null,
            exercise_end_time:"",
            contract_type:"",
            min_price:"",
            contract_first:"",
            constarct_second:"",
            description:"",
            delivery_type:"",
            exercise_price:"",
            ipo_time:"",
            end_time:"",
            execute_type:"",
            execute_platform:"",
            execute_weight_ratio:"",
            bail_type:"",
            bail_ratio:"",
            min_number:"千张",
            bail:"",
            bail_return:"",
            charge_unit:"",
            max_num_option:"",
            unit_valuation:"",
            pay_start_time:"",
            pay_end_time:""
        },
        axios:true,
        dialogTableVisible: false,
        formLabelWidth: '150px'
  }),
  created(){
      this.data.id = this.$route.params.id;
      if(!this.data.id){return}
      request.post(
       createRequestURI('product/data'),
       {"id":this.data.id},
       { validateStatus: status => status === 200 }
     ).then(response => {
        this.data = response.data;
        response.data.exercise_start_time = this.time(response.data.exercise_start_time)
        response.data.exercise_end_time = this.time(response.data.exercise_end_time)
        response.data.ipo_time = this.time(response.data.ipo_time)
        response.data.end_time = this.time(response.data.end_time)
        response.data.pay_start_time = this.time(response.data.pay_start_time)
        response.data.pay_end_time = this.time(response.data.pay_end_time)
        this.data = response.data;
       console.log(this.data);
       console.log(response.data);
     })
  },
  methods:{
      release(withdraw) {
      console.log(withdraw);
      let time = new Date(this.data.exercise_start_time).getTime() //行权开始时间
      let time1 = new Date(this.data.exercise_end_time).getTime() //行权结束时间
      let time3 = new Date(this.data.ipo_time).getTime()//上市时间
      let time4 = new Date(this.data.end_time).getTime()//期权时间
      let time5 = new Date(this.data.pay_start_time).getTime()//交易开始时间
      let time6 = new Date(this.data.pay_end_time).getTime()//交易结束时间
            if(!this.data.title){
                  alert("名称不能为空");
                  return;
            }  else if(!this.data.issue_price){
                   alert("发行价不能为空");
                   return;
            } else if(!this.data.exercise_start_time){
                   alert("缺少行权时间");
                   return;
            } else if(!this.data.exercise_end_time) {
                   alert("缺少行权时间");
                   return;
            } else if(!this.data.ipo_time){
                   alert("上市时间不能为空");
                   return;
            } else if(!this.data.end_time){
                   alert("期权到期时间不能为空");
                   return;
            } else if(!this.data.max_num_option){
                    alert("每人最多购买期权数量不能为空");
                    return;
            } else if(!this.data.exercise_price){
                   alert("行权价格不能为空");
                   return;
            } else if(!this.data.contract_first || !this.data.constarct_second){
                    alert("合约比较不能为空");
                    return;
            } else if(time4 <=time3){
                alert("期权到期时间必须大于上市时间");
                    return;
            } else if(time5 <= time3 || time6 >= time4){
                alert("交易期限必须大于上市时间小于期权到期时间");
                return;
            } else if(time <= time4){
                alert("行权开始时间必须大于期权到期时间");
                return;
            }
        this.axios = false
        request.post(
         createRequestURI('create/product'),
         {data:this.data, withdraw:withdraw},
         { validateStatus: status => status === 200 }
       ).then(response => {
          this.data = response.data;
          if(withdraw == 2) {
            this.$router.push("/List")
          } else{
            this.$router.push('/Draft')
          }
       }).catch(({ response: { data: { errors = ['加载认证类型失败'] } = {} } = {} }) => {
       });
      },
    time(value) {
        if(!value){return}
        return moment(parseInt(value)*1000).format('YYYY-MM-DD HH:mm');
    }
  }
};
export default AccountRelease;
</script>
