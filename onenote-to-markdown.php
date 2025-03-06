<?php
/*
Plugin Name: OneNote转Markdown工具
Plugin URI: https://github.com/kutongling/onenote-to-markdown
Description: 一个用于将OneNote笔记格式转换为Markdown格式的WordPress工具
Version: 1.0
Author: kutongling
Author URI: https://github.com/kutongling
License: MIT
Text Domain: onenote-to-markdown
*/

// 禁止直接访问此文件
if (!defined('ABSPATH')) {
    exit;
}

// 注册短代码 [onenote_markdown]
function onetmd_shortcode($atts = [], $content = null) {
    // 合并属性
    $atts = shortcode_atts(
        array(
            'title' => '将OneNote笔记转换为Markdown格式',
            'height' => '350',
        ),
        $atts,
        'onenote_markdown'
    );

    // 生成唯一ID
    $unique_id = 'ontmd_' . uniqid();
    
    // 输出HTML
    ob_start();
    ?>
    <div class="ontmd-tool" id="<?php echo esc_attr($unique_id); ?>">
        <h2 style="text-align:center; margin-bottom:20px;"><?php echo esc_html($atts['title']); ?></h2>
        
        <div style="margin-bottom:20px; padding:10px 15px; background:#f8f9fa; border-radius:5px; border:1px solid #e9ecef;">
            <label style="margin-right:15px;"><input type="checkbox" id="<?php echo esc_attr($unique_id); ?>_headings" checked> 保留中文编号标题</label>
            <label style="margin-right:15px;"><input type="checkbox" id="<?php echo esc_attr($unique_id); ?>_nesting" checked> 保留缩进层次</label>
            <label><input type="checkbox" id="<?php echo esc_attr($unique_id); ?>_clean" checked> 清理空行</label>
        </div>
        
        <div style="display:flex; flex-wrap:wrap; gap:20px; margin-bottom:20px;">
            <div style="flex:1; min-width:250px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <strong>OneNote原文</strong>
                    <div>
                        <button id="<?php echo esc_attr($unique_id); ?>_example" style="margin-right:5px; padding:4px 8px; background:none; border:1px solid #007bff; color:#007bff; border-radius:4px; cursor:pointer;">示例</button>
                        <button id="<?php echo esc_attr($unique_id); ?>_clear" style="padding:4px 8px; background:none; border:1px solid #6c757d; color:#6c757d; border-radius:4px; cursor:pointer;">清空</button>
                    </div>
                </div>
                <textarea id="<?php echo esc_attr($unique_id); ?>_input" style="width:100%; height:<?php echo esc_attr($atts['height']); ?>px; padding:12px; border:1px solid #ced4da; border-radius:4px; font-family:monospace; resize:vertical;" placeholder="粘贴您的OneNote文本到这里...&#10;（保留原始缩进格式）"></textarea>
            </div>
            
            <div style="flex:1; min-width:250px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <strong>Markdown结果</strong>
                    <button id="<?php echo esc_attr($unique_id); ?>_copy" style="padding:4px 8px; background:none; border:1px solid #28a745; color:#28a745; border-radius:4px; cursor:pointer;">复制</button>
                </div>
                <textarea id="<?php echo esc_attr($unique_id); ?>_output" style="width:100%; height:<?php echo esc_attr($atts['height']); ?>px; padding:12px; border:1px solid #ced4da; border-radius:4px; font-family:monospace; resize:vertical; background:#f8f9fa;" readonly placeholder="转换后的Markdown将显示在这里..."></textarea>
            </div>
        </div>
        
        <div id="<?php echo esc_attr($unique_id); ?>_status" style="display:none; margin:15px 0; padding:10px; border-radius:5px; text-align:center;"></div>
        
        <div style="text-align:center; margin:15px 0;">
            <button id="<?php echo esc_attr($unique_id); ?>_convert" style="padding:10px 25px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:500;">转换为Markdown</button>
        </div>
        
        <div style="text-align:center; margin-top:20px; font-size:0.9em; color:#6c757d;">
            <p>© 2023-2025 <a href="https://github.com/kutongling" target="_blank">kutongling</a></p>
        </div>

        <script>
        (function() {
            // 延迟初始化，避免可能的加载问题
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    initOneNoteTool_<?php echo esc_js($unique_id); ?>();
                }, 100);
            });

            // 初始化工具
            function initOneNoteTool_<?php echo esc_js($unique_id); ?>() {
                // 示例文本
                var exampleText = '一、"模糊"中探索：一条遥远的改编"渐近线"\n\t\t新中国成立初期，"文华""昆仑"两大私营电影公司，积极接身到讴歌新时代的创作中。\n\t\t\t1951年《武训传》批判运动\n二、主流叙事话语的建立：革命战争与革命历史题材的改编\n\t革命题材电影的特征\n\t\t1.注重对于"英雄人物"的塑造，"崭新的"人物形象手法\n\t\t\t（1）身份改写';
                
                // 获取元素
                var convertBtn = document.getElementById('<?php echo esc_js($unique_id); ?>_convert');
                var clearBtn = document.getElementById('<?php echo esc_js($unique_id); ?>_clear');
                var exampleBtn = document.getElementById('<?php echo esc_js($unique_id); ?>_example');
                var copyBtn = document.getElementById('<?php echo esc_js($unique_id); ?>_copy');
                var inputArea = document.getElementById('<?php echo esc_js($unique_id); ?>_input');
                var outputArea = document.getElementById('<?php echo esc_js($unique_id); ?>_output');
                var statusMsg = document.getElementById('<?php echo esc_js($unique_id); ?>_status');
                
                // 添加事件处理
                if (convertBtn) {
                    convertBtn.onclick = function() {
                        convertToMarkdown_<?php echo esc_js($unique_id); ?>();
                    };
                }
                
                if (clearBtn) {
                    clearBtn.onclick = function() {
                        inputArea.value = '';
                        inputArea.focus();
                    };
                }
                
                if (exampleBtn) {
                    exampleBtn.onclick = function() {
                        inputArea.value = exampleText;
                        showStatus_<?php echo esc_js($unique_id); ?>('已加载示例内容', 'info');
                    };
                }
                
                if (copyBtn) {
                    copyBtn.onclick = function() {
                        if (!outputArea.value) {
                            showStatus_<?php echo esc_js($unique_id); ?>('没有内容可复制', 'error');
                            return;
                        }
                        
                        outputArea.select();
                        try {
                            var successful = document.execCommand('copy');
                            if (successful) {
                                showStatus_<?php echo esc_js($unique_id); ?>('已复制到剪贴板！', 'success');
                            } else {
                                showStatus_<?php echo esc_js($unique_id); ?>('复制失败，请手动复制', 'error');
                            }
                        } catch (err) {
                            showStatus_<?php echo esc_js($unique_id); ?>('复制失败: ' + err, 'error');
                