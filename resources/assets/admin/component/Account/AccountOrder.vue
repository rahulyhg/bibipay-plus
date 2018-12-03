<template>
  <div class="container-fluid" style="margin-top:10px;">
  <div class="panel panel-default">
    <div class="panel-heading">搜索：
        <input type="text" v-model="orderId" placeholder="订单名称 ">
        <input type="text" v-model="bering" placeholder="订单ID ">
        <button class="btn btn-default" @click="getAdSpaces()">搜索</button>
    </div>
    <div class="panel-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>订单ID</th>
            <th>订单名称</th>
            <th>购买金额（usdt)</th>
            <th>状态</th>
            <th>购买日期</th>
            <th>购买人</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in data">
            <td>{{item.id}}</td>
            <td>{{item.product_name}}</td>
            <td>{{item.token_amount * item.token_price}}</td>
            <td>
                <a v-if='item.status == 0'>未付款</a>
                <a v-if='item.status == 1'>已支付</a>
                <a v-if='item.status == 2'>已行权</a>
                <a v-if='item.status == 3'>已失效</a>
                <a v-if='item.status == 4'>自动行权</a>
            </td>
            <td>{{item.buy_time}}</td>
            <td>{{item.user_name}}</td>
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
</style>


<script>
import lodash from "lodash";
import { mapGetters } from "vuex";
import request, { createRequestURI } from "../../util/request";

const AccountOrder = {
  /**
   * 定义当前组件状态数据
   *
   * @return {Object}
   * @author Seven Du <shiweidu@outlook.com>
   * @homepage http://medz.cn
   */
  data() {
    return {
        data:{},
        orderId:'',
        bering:'',
        total:0,
        page:0,
    }
  },
  mounted (){
    this.getAdSpaces();
  },
  methods: {
     search (){
        this.getAdSpaces();
     },
    getAdSpaces(val){
        request.post(
           createRequestURI('order/lists'),
           {page:this.page,product_name:this.orderId,product_num:this.bering},
           { validateStatus: status => status === 200 }
           ).then(response => {
             this.data = response.data.data;
             this.total = response.data.total;
           }).catch(({ response: { data: { errors = ['加载认证类型失败'] } = {} } = {} }) => {
        });
    },
     handleCurrentChange(val){
        this.page = val
        this.getAdSpaces()
     }
  }
};
export default AccountOrder;
</script>
