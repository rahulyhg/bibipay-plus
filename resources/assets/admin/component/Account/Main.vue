<template>
  <div>
	<nav class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">切换导航条</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <div class="navbar-brand">权证</div>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <!-- The settings nav. -->
          <ul class="nav navbar-nav">
	      	<!-- 权证 -->
	      	<router-link to="/account/users" tag="li" active-class="active">
	        	<a href="#">用户管理</a>
	      	</router-link>
	      	<router-link to="/account/Home" tag="li" active-class="active">
	        	<a href="#">账户管理</a>
	      	</router-link>
          <router-link to="/Order" tag="li" active-class="active">
	        	<a href="#">订单管理</a>
	      	</router-link>
          <router-link to="/Release" tag="li" active-class="active">
	        	<a href="#">发布管理</a>
	      	</router-link>
	      	<router-link to="/List" tag="li" active-class="active">
                <a href="#">发布信息管理</a>
            </router-link>
	      <router-link to="/Draft" tag="li" active-class="active">
                <a href="#">草稿箱</a>
            </router-link>
          <router-link to="/review" tag="li" active-class="active">
	        	<a href="#">审核</a>
	      	</router-link><router-link to="/cost" tag="li" active-class="active">
	        	<a href="#">设置手续费</a>
	      	</router-link>
          <!-- <router-link to="/Scale" tag="li" active-class="active">
	        	<a href="#">设置兑换比例</a>
	      	</router-link> -->
          </ul>
        </div>    
      </div>
    </nav>
    <router-view />
  </div>
</template>