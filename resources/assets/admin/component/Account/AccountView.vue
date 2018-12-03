<template>
    <div class="panel-heading">
    <!-- 搜索用户 -->
        <div class="form-horizontal">
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">名称：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.title" id="search-input-name"  disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">期权标的：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.token_type" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">合约类型：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control"  v-model="data.contract_type" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">计价单位：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.unit_valuation" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*发行价：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.issue_price" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">最小价格单位：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.min_price" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">合约比例</label>
                <div class="col-sm-8">
                    币种<input type="text" v-model="data.contract_first"> ：
                    合约<input type="text" v-model="data.constarct_second">
                    文本描述<input type="text" v-model="data.description">
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">交割方式：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.delivery_type" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*行权价格：</label>
                <div class="col-sm-4">
                    <input  type="text" v-model="data.exercise_price" disabled><span>RMB</span>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*上市时间：</label>
                <div class="col-sm-4">
                <span class="form-control" id="search-input-name" >{{data.ipo_time*1000 | time}}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*期权到期时间：</label>
                <div class="col-sm-4">
                <span class="form-control" id="search-input-name" >{{data.end_time*1000 | time}}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*交易期限：</label>
                <div class="col-sm-4">
                <span class="form-control" id="search-input-name" >{{data.pay_start_time*1000 | time}}</span>
                到
                <span class="form-control" id="search-input-name" >{{data.pay_end_time*1000 | time}}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*行权时间：</label>
                <div class="col-sm-4">
                    <span class="form-control" id="search-input-name" >{{data.exercise_start_time*1000 | time}}</span>
                    到
                    <span class="form-control" id="search-input-name" >{{data.exercise_end_time*1000 | time}}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">*每人最多购买期权数量：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.max_num_option" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">履行价格确认方式：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.execute_type" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">履行价格确认平台：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.execute_platform" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">履行价格权重比例：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.execute_weight_ratio" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">保证金标的</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.bail_type" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">保证金率：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.bail_ratio" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">最小数量单位：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.min_number" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">保证金：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.bail" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">其他要求：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" v-model="data.charge_unit" id="search-input-name" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="search-input-name" class="col-sm-2 control-label">保证金返还：</label>
                <div class="col-sm-4">
                    <input  type="text" class="form-control" id="search-input-name" disabled>
                </div>
            </div>
        </div>
        <div class="form-group">带有'*'的为必填项</div>

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
span{
    border:1px soild #ccc;
}
</style>


<script>
import lodash from "lodash";
import { mapGetters } from "vuex";
import request, { createRequestURI } from "../../util/request";
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
            exercise_start_time:"",
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
        }
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
       console.log(this.data)
     })
  }
};
export default AccountRelease;
</script>
