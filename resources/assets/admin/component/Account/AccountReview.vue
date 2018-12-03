<template>
  <div class="container-fluid" style="margin-top:10px;">

  <div class="container-fluid" style="margin-top:10px;">
    <div class="panel panel-default">
        <div class="col-sm-6">
            <label for="search-input-name" class="col-sm-3 control-label">USDT地址：</label>
            <div class="col-sm-9">
            {{prom.usdt_address}}
            </div>
        </div>
        <div class="col-sm-3">
            <label for="search-input-name" class="col-sm-6 control-label">USDT余额：</label>
            <div class="col-sm-6">
            {{prom.usdt_balance}}
            </div>
        </div>
        <div class="col-sm-3">
            <label for="search-input-name" class="col-sm-6 control-label">BTC余额：</label>
            <div class="col-sm-6">
            {{prom.btc_balance}}
            </div>
        </div>

    </div>
  </div>
  <div class="panel panel-default margin-top:10px;">
    <div class="panel-heading">
        <div class="form-inline">
            <div class="form-group">
                <label>币种 </label>
                <select class="form-control" @change='tokenSymbolValue()' v-model = "tokenSymbol">
                    <option value="all">--请选择--</option>
                    <option v-for="item in token" :value="item.id">{{item.token_name}}</option>
                </select>
            </div>
            <div class="form-group">
                <label>状态 </label>
                <select class="form-control" @change='statusValue()' v-model = "status">
                    <option value = "all">--请选择--</option>
                    <option value = "0">待审核</option>
                    <option value = "1">已通过</option>
                    <option value = "3">未通过</option>
                </select>
            </div>
            <div class="form-group">
                <router-link to="/TermReview" class="btn btn-default" tag="button">
                配置审核
                </router-link>
            </div>
        </div>
    </div>
    <div class="panel-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>序号</th>
            <th>币种</th>
            <th>提现地址</th>
            <th>提现金额</th>
            <th>手续费</th>
            <th>状态</th>
            <th>日期</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for = "item in data">
            <td>{{item.id}}</td>
            <td>
            <span v-for="list in token" v-if="list.id == item.type">{{list.token_name}}</span>
            </td>
            <td>{{item.address}}</td>
            <td>{{item.balance}}</td>
            <td>
            <span v-for="list in token" v-if="list.id == item.type">{{list.poundage}}</span>
            </td>
            <td>
            <span v-if="item.status == 0 ">审核中</span>
            <span v-if="item.status == 1 || item.status == 2 ">已通过</span>
            <span v-if="item.status == 3 ">未通过</span>
            </td>
                <td>{{item.created_time | time}}</td>
            <td ><a v-if="item.status == 0 " @click="release(item.id,1)">通过</a><a class="term" v-if="item.status == 0 " @click="release(item.id,3)">不通过</a></td>
          </tr>
        </tbody>
      </table>
      <!-- 分页 -->
        <el-pagination
          background
          :page-size="15"
          @current-change="handleCurrentChange"
          layout="prev, pager, next"
          :total="total">
        </el-pagination>
    </div>
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
.term{
    margin-left: 15px;
}
</style>


<script>
import lodash from "lodash";
import { mapGetters } from "vuex";
import request, { createRequestURI } from "../../util/request";


const AccountReview = {
  /**
   * 定义当前组件状态数据
   *
   * @return {Object}
   * @author Seven Du <shiweidu@outlook.com>
   * @homepage http://medz.cn
   */
   data () {
     return {
       data:{},
       token:{},
       status:'all',
       tokenSymbol:'all',
       total:0,
       prom:{},
       page:0
     }
   },
  mounted (){
    this.getAdSpaces();
    setInterval(() => {
        this.getCurry();
    }, 1000);
  },
   methods:{
     statusValue() {
         this.getAdSpaces()
     },
     tokenSymbolValue() {
          this.getAdSpaces()
      },
     getAdSpaces () {
       request.post(
         createRequestURI('forward/lists'),
         {page:this.page, status:this.status,token_symbol:this.tokenSymbol},
         { validateStatus: status => status === 200 }
       ).then(response => {
         this.data = response.data.data.data;
         this.token = response.data.poundage;
         this.total = response.data.data.total;
       })
   },
   release(id,status){
    request.post(
      createRequestURI('examine/operation'),
             {id:id,status:status},
             { validateStatus: status => status === 200 }
            ).then(response => {
            this.getAdSpaces();
            })
    },
     handleCurrentChange(val){
        this.page = val;
        this.getAdSpaces();
     },
     getCurry(){
        request.get(
            createRequestURI('account/information'),
             {validateStatus: status => status === 200 }
          ).then(response => {
                this.prom = response.data;
                console.log(this.prom)
          })
     }
   }
};
export default AccountReview;
</script>
