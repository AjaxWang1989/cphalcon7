/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2014 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  |          Vladimir Kolesnikov <vladimir@extrememember.com>              |
  |          ZhuZongXin <dreamsxin@qq.com>                                 |
  +------------------------------------------------------------------------+
*/

#include "php_phalcon.h"
#include "interned-strings.h"
#include "kernel/main.h"
#include "kernel/string.h"

zend_string *phalcon_interned_DELETE						= NULL;
zend_string *phalcon_interned_GET							= NULL;
zend_string *phalcon_interned_HEAD							= NULL;
zend_string *phalcon_interned_OPTIONS						= NULL;
zend_string *phalcon_interned_PATCH							= NULL;
zend_string *phalcon_interned_POST							= NULL;
zend_string *phalcon_interned_PUT							= NULL;
zend_string *phalcon_interned_action						= NULL;
zend_string *phalcon_interned_alias							= NULL;
zend_string *phalcon_interned_all							= NULL;
zend_string *phalcon_interned_allowEmpty					= NULL;
zend_string *phalcon_interned_arguments						= NULL;
zend_string *phalcon_interned_balias						= NULL;
zend_string *phalcon_interned_binary_op						= NULL;
zend_string *phalcon_interned_code							= NULL;
zend_string *phalcon_interned_column						= NULL;
zend_string *phalcon_interned_columns						= NULL;
zend_string *phalcon_interned_conditions					= NULL;
zend_string *phalcon_interned_controller					= NULL;
zend_string *phalcon_interned_delete						= NULL;
zend_string *phalcon_interned_dispatcher					= NULL;
zend_string *phalcon_interned_distinct						= NULL;
zend_string *phalcon_interned_domain						= NULL;
zend_string *phalcon_interned_escaper						= NULL;
zend_string *phalcon_interned_expr							= NULL;
zend_string *phalcon_interned_fields						= NULL;
zend_string *phalcon_interned_file							= NULL;
zend_string *phalcon_interned_filter						= NULL;
zend_string *phalcon_interned_functionCall					= NULL;
zend_string *phalcon_interned_group							= NULL;
zend_string *phalcon_interned_groupBy						= NULL;
zend_string *phalcon_interned_having						= NULL;
zend_string *phalcon_interned_items							= NULL;
zend_string *phalcon_interned_joins							= NULL;
zend_string *phalcon_interned_label							= NULL;
zend_string *phalcon_interned_left							= NULL;
zend_string *phalcon_interned_limit							= NULL;
zend_string *phalcon_interned_forupdate						= NULL;
zend_string *phalcon_interned_line							= NULL;
zend_string *phalcon_interned_message						= NULL;
zend_string *phalcon_interned_model							= NULL;
zend_string *phalcon_interned_models						= NULL;
zend_string *phalcon_interned_modelsCache					= NULL;
zend_string *phalcon_interned_modelsManager					= NULL;
zend_string *phalcon_interned_modelsMetadata				= NULL;
zend_string *phalcon_interned_modelsQuery					= NULL;
zend_string *phalcon_interned_modelsQueryBuilderForSelect	= NULL;
zend_string *phalcon_interned_modelsQueryBuilderForInsert	= NULL;
zend_string *phalcon_interned_modelsQueryBuilderForUpdate	= NULL;
zend_string *phalcon_interned_modelsQueryBuilderForDelete	= NULL;
zend_string *phalcon_interned_modelsCriteria				= NULL;
zend_string *phalcon_interned_modelsResultsetSimple			= NULL;
zend_string *phalcon_interned_modelsResultsetComplex		= NULL;
zend_string *phalcon_interned_module						= NULL;
zend_string *phalcon_interned_name							= NULL;
zend_string *phalcon_interned_namespace						= NULL;
zend_string *phalcon_interned_ns_alias						= NULL;
zend_string *phalcon_interned_number						= NULL;
zend_string *phalcon_interned_offset						= NULL;
zend_string *phalcon_interned_op							= NULL;
zend_string *phalcon_interned_order							= NULL;
zend_string *phalcon_interned_orderBy						= NULL;
zend_string *phalcon_interned_params						= NULL;
zend_string *phalcon_interned_parent						= NULL;
zend_string *phalcon_interned_paths							= NULL;
zend_string *phalcon_interned_qualified						= NULL;
zend_string *phalcon_interned_qualifiedName					= NULL;
zend_string *phalcon_interned_request						= NULL;
zend_string *phalcon_interned_response						= NULL;
zend_string *phalcon_interned_right							= NULL;
zend_string *phalcon_interned_router						= NULL;
zend_string *phalcon_interned_select						= NULL;
zend_string *phalcon_interned_self							= NULL;
zend_string *phalcon_interned_session						= NULL;
zend_string *phalcon_interned_sort							= NULL;
zend_string *phalcon_interned_source						= NULL;
zend_string *phalcon_interned_static						= NULL;
zend_string *phalcon_interned_sqlAlias						= NULL;
zend_string *phalcon_interned_table							= NULL;
zend_string *phalcon_interned_tables						= NULL;
zend_string *phalcon_interned_type							= NULL;
zend_string *phalcon_interned_update						= NULL;
zend_string *phalcon_interned_url							= NULL;
zend_string *phalcon_interned_value							= NULL;
zend_string *phalcon_interned_values						= NULL;
zend_string *phalcon_interned_where							= NULL;
zend_string *phalcon_interned_cookies						= NULL;
zend_string *phalcon_interned_annotations					= NULL;
zend_string *phalcon_interned_security						= NULL;
zend_string *phalcon_interned_crypt							= NULL;
zend_string *phalcon_interned_flash							= NULL;
zend_string *phalcon_interned_flashSession					= NULL;
zend_string *phalcon_interned_tag							= NULL;
zend_string *phalcon_interned_sessionBag					= NULL;
zend_string *phalcon_interned_eventsManager					= NULL;
zend_string *phalcon_interned_transactionManager			= NULL;
zend_string *phalcon_interned_assets						= NULL;
zend_string *phalcon_interned_rows							= NULL;
zend_string *phalcon_interned_view							= NULL;
zend_string *phalcon_interned_except						= NULL;
zend_string *phalcon_interned_app							= NULL;

PHALCON_STATIC void phalcon_init_interned_strings()
{
	phalcon_interned_DELETE							= SSL("DELETE");
	phalcon_interned_GET							= SSL("GET");
	phalcon_interned_HEAD							= SSL("HEAD");
	phalcon_interned_OPTIONS						= SSL("OPTIONS");
	phalcon_interned_PATCH							= SSL("PATCH");
	phalcon_interned_POST							= SSL("POST");
	phalcon_interned_PUT							= SSL("PUT");
	phalcon_interned_action							= SSL("action");
	phalcon_interned_alias							= SSL("alias");
	phalcon_interned_all							= SSL("all");
	phalcon_interned_allowEmpty						= SSL("allowEmpty");
	phalcon_interned_arguments						= SSL("arguments");
	phalcon_interned_balias							= SSL("balias");
	phalcon_interned_binary_op						= SSL("binary-op");
	phalcon_interned_code							= SSL("code");
	phalcon_interned_column							= SSL("column");
	phalcon_interned_columns						= SSL("columns");
	phalcon_interned_conditions						= SSL("conditions");
	phalcon_interned_controller						= SSL("controller");
	phalcon_interned_delete							= SSL("delete");
	phalcon_interned_dispatcher						= SSL("dispatcher");
	phalcon_interned_distinct						= SSL("distinct");
	phalcon_interned_domain							= SSL("domain");
	phalcon_interned_escaper						= SSL("escaper");
	phalcon_interned_expr							= SSL("expr");
	phalcon_interned_fields							= SSL("fields");
	phalcon_interned_file							= SSL("file");
	phalcon_interned_filter							= SSL("filter");
	phalcon_interned_functionCall					= SSL("functionCall");
	phalcon_interned_group							= SSL("group");
	phalcon_interned_groupBy						= SSL("groupBy");
	phalcon_interned_having							= SSL("having");
	phalcon_interned_items							= SSL("items");
	phalcon_interned_joins							= SSL("joins");
	phalcon_interned_label							= SSL("label");
	phalcon_interned_left							= SSL("left");
	phalcon_interned_limit							= SSL("limit");
	phalcon_interned_forupdate						= SSL("forupdate");
	phalcon_interned_line							= SSL("line");
	phalcon_interned_message						= SSL("message");
	phalcon_interned_model							= SSL("model");
	phalcon_interned_models							= SSL("models");
	phalcon_interned_modelsCache					= SSL("modelsCache");
	phalcon_interned_modelsManager					= SSL("modelsManager");
	phalcon_interned_modelsMetadata					= SSL("modelsMetadata");
	phalcon_interned_modelsQuery					= SSL("modelsQuery");
	phalcon_interned_modelsQueryBuilderForSelect	= SSL("modelsQueryBuilderForSelect");
	phalcon_interned_modelsQueryBuilderForInsert	= SSL("modelsQueryBuilderForInsert");
	phalcon_interned_modelsQueryBuilderForUpdate	= SSL("modelsQueryBuilderForUpdate");
	phalcon_interned_modelsQueryBuilderForDelete	= SSL("modelsQueryBuilderForDelete");
	phalcon_interned_modelsCriteria					= SSL("modelsCriteria");
	phalcon_interned_modelsResultsetSimple			= SSL("modelsResultsetSimple");
	phalcon_interned_modelsResultsetComplex			= SSL("modelsResultsetComplex");
	phalcon_interned_module							= SSL("module");
	phalcon_interned_name							= SSL("name");
	phalcon_interned_namespace						= SSL("namespace");
	phalcon_interned_ns_alias						= SSL("ns-alias");
	phalcon_interned_number							= SSL("number");
	phalcon_interned_offset							= SSL("offset");
	phalcon_interned_op								= SSL("op");
	phalcon_interned_order							= SSL("order");
	phalcon_interned_orderBy						= SSL("orderBy");
	phalcon_interned_params							= SSL("params");
	phalcon_interned_parent							= SSL("parent");
	phalcon_interned_paths							= SSL("paths");
	phalcon_interned_qualified						= SSL("qualified");
	phalcon_interned_qualifiedName					= SSL("qualifiedName");
	phalcon_interned_request						= SSL("request");
	phalcon_interned_response						= SSL("response");
	phalcon_interned_right							= SSL("right");
	phalcon_interned_router							= SSL("router");
	phalcon_interned_select							= SSL("select");
	phalcon_interned_self							= SSL("self");
	phalcon_interned_session						= SSL("session");
	phalcon_interned_sort							= SSL("sort");
	phalcon_interned_source							= SSL("source");
	phalcon_interned_static							= SSL("static");
	phalcon_interned_sqlAlias						= SSL("sqlAlias");
	phalcon_interned_table							= SSL("table");
	phalcon_interned_tables							= SSL("tables");
	phalcon_interned_type							= SSL("type");
	phalcon_interned_update							= SSL("update");
	phalcon_interned_url							= SSL("url");
	phalcon_interned_value							= SSL("value");
	phalcon_interned_values							= SSL("values");
	phalcon_interned_where							= SSL("where");
	phalcon_interned_rows							= SSL("rows");
	phalcon_interned_cookies						= SSL("cookies");
	phalcon_interned_annotations					= SSL("annotations");
	phalcon_interned_security						= SSL("security");
	phalcon_interned_crypt							= SSL("crypt");
	phalcon_interned_flash							= SSL("flash");
	phalcon_interned_flashSession					= SSL("flashSession");
	phalcon_interned_tag							= SSL("tag");
	phalcon_interned_sessionBag						= SSL("sessionBag");
	phalcon_interned_eventsManager					= SSL("eventsManager");
	phalcon_interned_transactionManager				= SSL("transactions");
	phalcon_interned_assets							= SSL("assets");
	phalcon_interned_view							= SSL("view");
	phalcon_interned_except							= SSL("except");
	phalcon_interned_app							= SSL("app");
}

PHALCON_STATIC void phalcon_release_interned_strings()
{
	zend_string_release(phalcon_interned_DELETE);
	zend_string_release(phalcon_interned_GET);
	zend_string_release(phalcon_interned_HEAD);
	zend_string_release(phalcon_interned_OPTIONS);
	zend_string_release(phalcon_interned_PATCH);
	zend_string_release(phalcon_interned_POST);
	zend_string_release(phalcon_interned_PUT);
	zend_string_release(phalcon_interned_action);
	zend_string_release(phalcon_interned_alias);
	zend_string_release(phalcon_interned_all);
	zend_string_release(phalcon_interned_allowEmpty);
	zend_string_release(phalcon_interned_arguments);
	zend_string_release(phalcon_interned_balias);
	zend_string_release(phalcon_interned_binary_op);
	zend_string_release(phalcon_interned_code);
	zend_string_release(phalcon_interned_column);
	zend_string_release(phalcon_interned_columns);
	zend_string_release(phalcon_interned_conditions);
	zend_string_release(phalcon_interned_controller);
	zend_string_release(phalcon_interned_delete);
	zend_string_release(phalcon_interned_dispatcher);
	zend_string_release(phalcon_interned_distinct);
	zend_string_release(phalcon_interned_domain);
	zend_string_release(phalcon_interned_escaper);
	zend_string_release(phalcon_interned_expr);
	zend_string_release(phalcon_interned_fields);
	zend_string_release(phalcon_interned_file);
	zend_string_release(phalcon_interned_filter);
	zend_string_release(phalcon_interned_functionCall);
	zend_string_release(phalcon_interned_group);
	zend_string_release(phalcon_interned_groupBy);
	zend_string_release(phalcon_interned_having);
	zend_string_release(phalcon_interned_items);
	zend_string_release(phalcon_interned_joins);
	zend_string_release(phalcon_interned_label);
	zend_string_release(phalcon_interned_left);
	zend_string_release(phalcon_interned_limit);
	zend_string_release(phalcon_interned_forupdate);
	zend_string_release(phalcon_interned_line);
	zend_string_release(phalcon_interned_message);
	zend_string_release(phalcon_interned_model);
	zend_string_release(phalcon_interned_models);
	zend_string_release(phalcon_interned_modelsCache);
	zend_string_release(phalcon_interned_modelsManager);
	zend_string_release(phalcon_interned_modelsMetadata);
	zend_string_release(phalcon_interned_modelsQuery);
	zend_string_release(phalcon_interned_modelsQueryBuilderForSelect);
	zend_string_release(phalcon_interned_modelsQueryBuilderForInsert);
	zend_string_release(phalcon_interned_modelsQueryBuilderForUpdate);
	zend_string_release(phalcon_interned_modelsQueryBuilderForDelete);
	zend_string_release(phalcon_interned_modelsCriteria);
	zend_string_release(phalcon_interned_modelsResultsetSimple);
	zend_string_release(phalcon_interned_modelsResultsetComplex);
	zend_string_release(phalcon_interned_module);
	zend_string_release(phalcon_interned_name);
	zend_string_release(phalcon_interned_namespace);
	zend_string_release(phalcon_interned_ns_alias);
	zend_string_release(phalcon_interned_number);
	zend_string_release(phalcon_interned_offset);
	zend_string_release(phalcon_interned_op);
	zend_string_release(phalcon_interned_order);
	zend_string_release(phalcon_interned_orderBy);
	zend_string_release(phalcon_interned_params);
	zend_string_release(phalcon_interned_parent);
	zend_string_release(phalcon_interned_paths);
	zend_string_release(phalcon_interned_qualified);
	zend_string_release(phalcon_interned_qualifiedName);
	zend_string_release(phalcon_interned_request);
	zend_string_release(phalcon_interned_response);
	zend_string_release(phalcon_interned_right);
	zend_string_release(phalcon_interned_router);
	zend_string_release(phalcon_interned_select);
	zend_string_release(phalcon_interned_self);
	zend_string_release(phalcon_interned_session);
	zend_string_release(phalcon_interned_sort);
	zend_string_release(phalcon_interned_source);
	zend_string_release(phalcon_interned_static);
	zend_string_release(phalcon_interned_sqlAlias);
	zend_string_release(phalcon_interned_table);
	zend_string_release(phalcon_interned_tables);
	zend_string_release(phalcon_interned_type);
	zend_string_release(phalcon_interned_update);
	zend_string_release(phalcon_interned_url);
	zend_string_release(phalcon_interned_value);
	zend_string_release(phalcon_interned_values);
	zend_string_release(phalcon_interned_where);
	zend_string_release(phalcon_interned_rows);
	zend_string_release(phalcon_interned_cookies);
	zend_string_release(phalcon_interned_annotations);
	zend_string_release(phalcon_interned_security);
	zend_string_release(phalcon_interned_crypt);
	zend_string_release(phalcon_interned_flash);
	zend_string_release(phalcon_interned_flashSession);
	zend_string_release(phalcon_interned_tag);
	zend_string_release(phalcon_interned_sessionBag);
	zend_string_release(phalcon_interned_eventsManager);
	zend_string_release(phalcon_interned_transactionManager);
	zend_string_release(phalcon_interned_assets);
	zend_string_release(phalcon_interned_view);
	zend_string_release(phalcon_interned_except);
	zend_string_release(phalcon_interned_app);
}
