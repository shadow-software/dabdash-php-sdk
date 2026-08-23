# ShadowSoftware\DabDash\WriteApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**bundleList()**](WriteApi.md#bundleList) | **POST** /api/v1/tools/bundle_list | List a tenant&#39;s bundle deals (\&quot;mix &amp; match\&quot; — e.g. \&quot;buy 4 for $77\&quot;) with id, name, trigger quantity, discount type/value, active state, schedule window, and attached variation count. Bundles are the live cart engine (MixMatchService): a bundle fires when a cart holds at least &#x60;quantity&#x60; units across its attached variations. Always call this before bundle_upsert to get the bundle id and confirm the current discount configuration.  IMPORTANT: this reads the &#x60;bundles&#x60; table — the source of truth the storefront cart uses. It is NOT the legacy &#x60;mix_match_rules&#x60; tenant setting that promotion_audit / product_inspect surface; those are stale display-only data. Trust this tool for what actually applies at checkout.  discount_type:   percent      → discount_value is a percentage 0–100, applied per unit.   fixed        → discount_value is dollars off PER UNIT.   fixed_total  → discount_value is the dollar TOTAL for the whole set (\&quot;$77 for 4\&quot;). |
| [**bundleUpsert()**](WriteApi.md#bundleUpsert) | **POST** /api/v1/tools/bundle_upsert | Create or update a bundle deal (mix &amp; match) on behalf of a tenant. Bundles are the live cart engine: a bundle fires when a cart holds at least &#x60;quantity&#x60; units across its attached variations.  UPDATE MODE (bundle_id provided):   Edits the bundle. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no bundle_id):   Creates a new bundle. name, quantity, discount_type, and discount_value are required.  DISCOUNT VALUE UNITS — read carefully, this is the common mistake:   discount_type &#x3D; \&quot;percent\&quot;      → discount_value is a percentage 0–100 (e.g. 20 &#x3D; 20% off each unit).   discount_type &#x3D; \&quot;fixed\&quot;        → discount_value is DOLLARS off PER UNIT (e.g. 5 &#x3D; $5 off each).   discount_type &#x3D; \&quot;fixed_total\&quot;  → discount_value is the DOLLARS TOTAL for the whole set                                     (e.g. quantity&#x3D;4, discount_value&#x3D;77 → \&quot;any 4 for $77\&quot;).   For fixed and fixed_total, pass dollars (e.g. 77 or 77.00) — the tool stores cents internally.   For percent, pass the percentage (e.g. 20), NOT a fraction.  VARIATIONS:   variation_ids + variation_mode control which product variations the bundle applies to.   mode \&quot;replace\&quot; (default) sets membership to exactly variation_ids; \&quot;add\&quot; attaches them to the   existing set; \&quot;detach\&quot; removes them. Variations not owned by the tenant are ignored.   Omit variation_ids entirely to leave membership untouched.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass null/omit   for an always-on bundle.  Always call bundle_list first to get the bundle_id and confirm the current configuration. |
| [**campaignApplyTemplate()**](WriteApi.md#campaignApplyTemplate) | **POST** /api/v1/tools/campaign_apply_template | Apply a built-in system email template to a DRAFT campaign, replacing its html_body with the rendered, tenant-branded design. This mirrors the \&quot;Choose template\&quot; action in the vendor admin.  The template is rendered with the tenant&#39;s own branding (theme colour, logo, name, address, phone), then sanitized and written to the campaign&#39;s html_body. The {{unsubscribe_url}} token is preserved. The design ships with placeholder copy ([Product name], $00, \&quot;Your headline here\&quot;, etc.) — after applying, use campaign_upsert to set the real html_body with this tenant&#39;s products, prices, and offers, or hand off to the vendor to fill in.  Only DRAFT campaigns can have a template applied. To discover valid template_id values, omit template_id (or pass an unknown one) and the tool returns the list of available templates.  Typical flow: campaign_upsert (create draft) → campaign_apply_template (lay down the design) → campaign_upsert (replace html_body with real data) → campaign_send_test (preview). |
| [**campaignControl()**](WriteApi.md#campaignControl) | **POST** /api/v1/tools/campaign_control | Pause or resume a vendor email/SMS campaign.  pause: sets status&#x3D;paused with paused_reason&#x3D;manual. Works from sending or recovering. Pending recipients stay pending; the dispatcher skips this campaign so sibling sending campaigns can use the shared throttle.  resume: from paused → sending (clears pause fields). From recovering → sending and requeues transport-failed recipients (same as auto-resume after a healthy webhook health check).  dry_run defaults TRUE. Always confirm tenant_slug and campaign_id first. |
| [**campaignRecipientsRequeue()**](WriteApi.md#campaignRecipientsRequeue) | **POST** /api/v1/tools/campaign_recipients_requeue | Unstick a vendor email/SMS campaign that auto-paused or stalled mid-send.  Resets failed recipients (and optionally stale \&quot;sending\&quot; rows) back to pending, clears webhook_consecutive_failures / paused_reason / last_error, and sets the campaign status to sending so the dispatcher continues. Already-sent recipients are never touched.  Pass retry_sending_log_errors&#x3D;true to retry every recipient that appears in the campaign sending log with a retryable error (502/timeout/SMTP soft fail, etc.). Those jobs dispatch immediately (up to dispatch_limit) so they are not stuck behind a large pending queue. The log rows stay — they are diagnostic history.  Use when a webhook campaign froze after consecutive endpoint errors (or soft SMTP failures that were misclassified before the soft-fail fix) and pending + failed recipients remain.  dry_run defaults to TRUE — first call reports counts only. Pass dry_run&#x3D;false to apply. Always confirm tenant_slug with tenant_list and campaign_id with the vendor. |
| [**campaignSendTest()**](WriteApi.md#campaignSendTest) | **POST** /api/v1/tools/campaign_send_test | Send a single test copy of a campaign so the design and copy can be reviewed before the real send.  Email campaigns go out through the tenant&#39;s own SMTP settings — exactly the path a real send uses. Pass to_email. Requires complete SMTP settings.  Text (SMS) campaigns go out through the tenant&#39;s connected Twilio account. Pass to_phone instead of to_email. Requires Twilio to be connected.  This is a preview only: it does NOT start the campaign, does NOT touch the audience, and does NOT record any send/open/click stats. Personalization tokens render with sample values; unsubscribe links point at harmless test URLs.  Works on a campaign in any status (a draft preview is the common case). Always confirm the campaign_id with the vendor first. |
| [**campaignSetImage()**](WriteApi.md#campaignSetImage) | **POST** /api/v1/tools/campaign_set_image | Drop an image from the tenant&#39;s media library into a DRAFT campaign — no link copying, no manual HTML. Give it a campaign_id and a media_id (from the tenant&#39;s own library) and it fills an image placeholder in the campaign body with that image, using the library image&#39;s own public URL and alt text. The URL is on the tenant&#39;s public disk, so it passes the campaign HTML sanitizer untouched and renders in email clients.  WHICH SLOT IT FILLS:   System templates render image placeholders labelled \&quot;Add image\&quot;. By default this tool fills the   FIRST remaining placeholder. Pass slot_index (1-based) to target a specific placeholder when a   template has more than one. If the body has no placeholder left, the image is appended at the end.  REQUIREMENTS:   - The campaign must be a DRAFT and belong to the tenant.   - The media asset must belong to the tenant and be public (private assets have no shareable URL).  Typical flow: campaign_upsert (create) → campaign_apply_template (design with placeholders) → campaign_set_image (fill each placeholder) → campaign_upsert (real copy) → campaign_send_test. |
| [**campaignUpsert()**](WriteApi.md#campaignUpsert) | **POST** /api/v1/tools/campaign_upsert | Create or edit a vendor email campaign on behalf of a tenant. This is the vendor → their-customers campaign universe (NOT the platform drip-to-leads system). Completed campaigns cannot be edited.  CREATE MODE (no campaign_id):   Creates a new draft. For email campaigns, &#x60;name&#x60; and &#x60;subject&#x60; are required; &#x60;html_body&#x60; is optional   on create — omit it to start blank and apply a system template afterwards with campaign_apply_template.   For text (SMS) campaigns, set channel to \&quot;sms\&quot; and pass &#x60;sms_body&#x60; (name required). Twilio must be   connected on the tenant.  UPDATE MODE (campaign_id provided):   Edits an existing campaign. Only the fields you pass are changed; omitted fields are left as-is.   Drafts accept all fields. Sending or paused campaigns accept content fields only — for email that is   name, subject, html_body, plain_body, fallback_first_name; for text that is name, sms_body,   and fallback_first_name. Sending campaigns keep   running and unreached recipients get the latest version. Scheduled, cancelled, and failed campaigns   must be edited from the vendor admin.  HTML BODY (email only):   Pass the full email HTML in &#x60;html_body&#x60;. It is sanitized exactly like the vendor admin editor:   a full document (&lt;!DOCTYPE …&gt; / &lt;html&gt;) keeps its table-based structure; a fragment is run   through the stricter inline allowlist. Use the literal token {{unsubscribe_url}} where the   unsubscribe link should appear — it is replaced per-recipient at send time and a List-Unsubscribe   header is added automatically. Personalization tokens {{first_name}} and {{last_name}} are also   replaced at send time. When {{first_name}} is blank, fallback_first_name is used (default \&quot;there\&quot;   when unset). Pass fallback_first_name as \&quot;\&quot; to clear an override.  SMS BODY (text only):   Pass the message in &#x60;sms_body&#x60; (max 1600 chars). Tokens {{first_name}} and {{last_name}} are   replaced at send time (same first-name fallback as email). Text campaigns only target opted-in shop customers.  AUDIENCE / MODE (email only):   mode is \&quot;smtp\&quot; (default) or \&quot;webhook\&quot;. audience_includes_customers (default true) targets the   tenant&#39;s own customers. This tool does not send anything — use campaign_send_test to preview,   then the vendor sends from the admin.  Always confirm the tenant_slug with tenant_list first. To populate a draft from a built-in design, create it here, then call campaign_apply_template. |
| [**catalogCollapse()**](WriteApi.md#catalogCollapse) | **POST** /api/v1/tools/catalog_collapse | Merges a group of size-split products into ONE product with size options.  Turns \&quot;Blue Dream - 3.5G\&quot;, \&quot;Blue Dream - 7G\&quot;, \&quot;Blue Dream - 28G\&quot; (three separate products, each with one \&quot;Default\&quot; option) into a single \&quot;Blue Dream\&quot; product that sells by weight with 3.5g / 7g / 28g options. Stock is added together into one shared pool, and each old price becomes the price of its size.  Run catalog_flattening_audit first to see the groups and their exact base_name.  SAFETY: - dry_run defaults to TRUE. Nothing changes until you pass dry_run&#x3D;false. The dry run returns   the exact sizes, prices, stock, and which products would be removed. - Past orders are never affected — they keep the product name and price the customer saw. - Refused when two products in the group claim the same size, or when any product belongs to   another store. - This removes the duplicate products. It cannot be undone from here. |
| [**categoryManage()**](WriteApi.md#categoryManage) | **POST** /api/v1/tools/category_manage | List, create, update, or delete a tenant&#39;s storefront categories.  ACTIONS:   list   (default): return every category with id, name, slug, parent, sort_order,          is_active, is_featured, and image_url. Always call this first to find a          category_id before update/delete, and to check for slug collisions before create.   create: requires name (slug is auto-generated from name if omitted).   update: requires category_id. Only the fields you pass are changed.   delete: requires category_id and confirm&#x3D;true. Refuses if the category still has          products or children attached (detach or reassign them first).  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites branding onto). Omit both to leave images untouched.  Always call action&#x3D;list first to confirm category_id / slug before update or delete. |
| [**contactCleanup()**](WriteApi.md#contactCleanup) | **POST** /api/v1/tools/contact_cleanup | Removes contacts that failed email validation from an imported list.  Typical use: a CSV import brought in thousands of addresses, the validator graded them, and the list is now full of dead addresses that will bounce and hurt sending reputation.  SAFETY: - dry_run defaults to TRUE. Nothing is deleted until you pass dry_run&#x3D;false. The dry run   returns exactly how many contacts match and a sample of them. - Only statuses \&quot;invalid\&quot; and \&quot;risky\&quot; can be purged. \&quot;unknown\&quot; means the validator has not   graded that contact YET — those are never deleted, and asking for them is refused. - Scoped to one contact list when list_id is given, otherwise the whole store. - Contacts are deleted, not unsubscribed. This cannot be undone from here. |
| [**couponUpsert()**](WriteApi.md#couponUpsert) | **POST** /api/v1/tools/coupon_upsert | Create or update a discount coupon on behalf of a tenant. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_upsert for those).  UPDATE MODE (coupon_id provided):   Edits the coupon. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no coupon_id):   Creates a new coupon. code, type, and value are required.  VALUE UNITS:   type &#x3D; \&quot;percentage\&quot;     → value is a percentage 0-100 off the order subtotal.   type &#x3D; \&quot;fixed\&quot;          → value is DOLLARS off the order subtotal (e.g. 10 &#x3D; $10 off).   type &#x3D; \&quot;free_delivery\&quot;  → value is ignored (pass 0); this type only waives the delivery fee.  min_order is a dollar minimum order subtotal required to use the coupon (pass dollars, e.g. 25 for a $25 minimum — the tool stores cents internally). Omit or pass 0 for no minimum.  freebie_id links this coupon to an existing Freebie (see freebie_list/freebie_upsert), turning it into a \&quot;code-triggered freebie\&quot;: a customer who applies the code while below min_order has the code parked (kept attached, not rejected) and sees cart progress toward unlocking it, instead of the code being discarded. Pass null to unlink. The freebie must belong to the same tenant.  limit_match_by (\&quot;email\&quot;|\&quot;phone\&quot;|\&quot;both\&quot;) controls how max_uses_per_customer is enforced. Using \&quot;phone\&quot; or \&quot;both\&quot; REQUIRES the tenant&#39;s \&quot;Require phone at checkout\&quot; setting to be on — otherwise the update is rejected, since customers without a phone on file could otherwise reuse the coupon past its per-customer limit.  SCHEDULE:   starts_at / expires_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass   null/omit for an always-on coupon.  PRODUCT/CATEGORY SCOPING:   applies_to&#x3D;\&quot;products\&quot; or \&quot;categories\&quot; restricts the discount to matching cart lines only —   checkout math is fully scope-aware (a scoped coupon never discounts the whole cart). Pass   applies_to_ids as an array of product ids (when applies_to&#x3D;\&quot;products\&quot;) or category ids (when   applies_to&#x3D;\&quot;categories\&quot;) — all ids must belong to this tenant. Passing applies_to&#x3D;\&quot;products\&quot;/   \&quot;categories\&quot; with an empty or omitted applies_to_ids behaves the same as \&quot;all\&quot; (no scope   configured yet). Switching back to applies_to&#x3D;\&quot;all\&quot; does not automatically clear a   previously-set applies_to_ids — pass applies_to_ids&#x3D;[] explicitly to clear it.  SUBSCRIPTION MOUNTING DISCOUNT LADDERS (subscribe-and-save retention):   A coupon can carry a \&quot;mounting ladder\&quot; so its discount climbs with each successive order a   customer&#39;s delivery subscription generates, pegging at a ceiling — e.g. order 1 &#x3D; 0% off,   then +5% every order up to a 20% cap. This only affects orders generated for a subscription   linked to this coupon (DeliverySubscription.coupon_id) — it has no effect on ordinary   one-off checkout use of the code, which still uses type/value as normal.    subscription_ladder_id: link to an existing ladder (from another coupon) by id, or pass null   to unlink. Must belong to the same tenant.    ladder_start_percent / ladder_step_percent / ladder_cap_percent / ladder_start_order_index:   pass any of these to CREATE a new ladder inline (on coupon create) or EDIT the tiers of the   ladder already linked to this coupon (on update) — do not combine with subscription_ladder_id   in the same call. All four are whole-number percentages (0-100) except start_order_index,   which is the 1-based order number the climb begins at (orders before it stay at   ladder_start_percent). Omitted ladder_* fields default to 0% start / 5% step / 20% cap /   order 1 on create; on update, only the fields you pass are changed.  Always call coupon_list first to get the coupon_id and confirm the current configuration. |
| [**customerUpdate()**](WriteApi.md#customerUpdate) | **POST** /api/v1/tools/customer_update | Update a customer&#39;s contact fields (name, email, phone) and/or suppress marketing consent (email_opt_out, sms_marketing_opt_out, sms_notifications_muted — one-way, cannot un-suppress). Verification, loyalty, and other DabDash-owned fields cannot be set here. |
| [**freebieUpsert()**](WriteApi.md#freebieUpsert) | **POST** /api/v1/tools/freebie_upsert | Create or update a freebie rule (\&quot;spend $X, get a free item\&quot;) on behalf of a tenant. Freebies are evaluated on every cart change: a rule fires once its spend_threshold is met, adding &#x60;quantity&#x60; of the configured product/variation to the cart (distinct from bundles, which fire on cart CONTENTS/quantity — use bundle_upsert for those).  UPDATE MODE (freebie_id provided):   Edits the freebie. Only the fields you pass are changed; omitted fields are left as-is,   EXCEPT category_ids, which — like bundle_upsert&#39;s variation_ids — fully replaces the category   set whenever passed (pass an empty array to clear all categories).  CREATE MODE (no freebie_id):   Creates a new freebie. name, product_id, spend_threshold, and quantity are required.  product_id and variation_id (if given) MUST belong to the same tenant — foreign ids are rejected, not silently ignored (unlike bundle_upsert&#39;s variation_ids, since a freebie needs exactly one product to give away, not a set).  spend_threshold is entered in DOLLARS (e.g. 50 for a $50 minimum spend) — the tool stores cents internally.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order.   false  → this rule cannot combine with other freebie rules.   Does NOT multiply this rule&#39;s own quantity by how many multiples of spend_threshold the cart   reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass   null/omit for an always-on freebie.  Always call freebie_list first to get the freebie_id and confirm the current configuration. |
| [**mediaCompose()**](WriteApi.md#mediaCompose) | **POST** /api/v1/tools/media_compose | Build a finished campaign/storefront creative ON THE SERVER: take a base picture, drop the vendor&#39;s logo on top, add a headline and subtitle, and save the RESULT straight into the tenant&#39;s media library. Returns a media_id + public URL ready to place with campaign_set_image, category_manage, widget_manage, tenant_branding_manage, product_image_assign, or tenant_blog_upsert.  PROVIDE THE BASE (exactly one):   - base_media_id — preferred for tenant-owned photos already in the library (media_list /     media_upload). Keeps the source visible in Admin → Media.   - base_url      — public https URL for external/generated artwork. Only the composed     OUTPUT is saved as a library row; the raw URL bytes are not separately ingested. Prefer     media_upload → base_media_id when the photo should remain in the media library (#184).  PROVIDE THE LOGO (optional, at most one):   - logo_media_id / logo_url — same two options as the base (prefer logo_media_id for the     tenant logo so it stays linked in Media).   THE LOGO IS NEVER ALTERED. It is scaled to fit (aspect ratio locked) and placed as-is —   its colours, strokes and transparency are left exactly as supplied. For legibility on busy   artwork use the &#x60;scrim&#x60; option, which darkens the area BEHIND the logo and text.  LAYOUT: logo on top, headline under it, subtitle under that — the block is centred horizontally and positioned with logo_position (top / center / bottom). Sizes are given as a fraction of the base image width so they scale with any canvas. Long text is word-wrapped automatically; the response&#39;s &#x60;wrapped&#x60; flag tells you when that happened so you can shorten it.  CONSTRAINTS: sources max 5 MB, JPEG/PNG/WebP/GIF, max 8000x8000. The finished image goes through the same pipeline as media_upload — re-encoded to WebP, resized to fit, de-duplicated by content — so identical inputs return the existing media_id instead of a duplicate.  Typical flow: media_upload (or media_list) → media_compose → place with media_id.  STOREFRONT SWAG MODE (category_id or widget_id): instead of the manual layout above, pass category_id or widget_id to run the platform&#39;s own storefront branding pipeline (SwagImagesService) against that exact category or widget — the same compositor used by the dev seeder and vendor admin: tenant logo eyebrow, Bebas Neue headline (shrunk to fit long names automatically), brand-colour accent rule, and a sub-label (the tenant&#39;s live domain for categories, the widget&#39;s own subtitle for widgets). Pass base_media_id or base_url to set/replace the row&#39;s base_image_path first; omit both to recompose from whatever base_image_path is already saved. Saves directly to image_path on that category or widget — this is an internal/admin tool, not exposed to vendors. |
| [**mediaList()**](WriteApi.md#mediaList) | **POST** /api/v1/tools/media_list | List the images in a tenant&#39;s media library — id, public URL, dimensions, filename, folder, and alt text. Use this to find the media_id of an image to place into a campaign with campaign_set_image, instead of guessing. Returns newest first.  Filter with &#x60;folder&#x60; (exact match) or &#x60;search&#x60; (filename substring). &#x60;visibility&#x60; defaults to \&quot;public\&quot; (the emailable assets); pass \&quot;all\&quot; or \&quot;private\&quot; to widen. Confirm the tenant_slug with tenant_list first. |
| [**mediaUpload()**](WriteApi.md#mediaUpload) | **POST** /api/v1/tools/media_upload | Upload an image into a tenant&#39;s media library (the same library the vendor admin uses). The image is ingested through the platform&#39;s shared media pipeline — re-encoded to WebP, resized to fit, EXIF- oriented, content-addressed for dedup — and a media_assets row is created. It ALWAYS lands in the library, and the tool returns the new media_id and public URL.  PLACE the media_id with the matching tool (never hotlink the URL as a product/blog/hero path):   - campaign_set_image — email campaign HTML   - category_manage / widget_manage — storefront images (media_id / base_media_id)   - tenant_branding_manage — store logo (media_id) or homepage hero (hero_media_id)   - product_image_assign — product featured / gallery   - tenant_blog_upsert — vendor blog featured image   - media_compose — base_media_id / logo_media_id for composed creatives  PROVIDE THE IMAGE ONE OF THREE WAYS (exactly one):   - source_url    — fetch the image from a public http(s) URL (preferred for remote agents).   - source_path   — read a local file path on the server/agent host (often broken on prod FS).   - source_base64 — raw base64 of the image bytes (no data: prefix needed; a data: prefix is stripped).  CONSTRAINTS: max 5 MB; JPEG, PNG, WebP, or GIF. Identical bytes already in the library are de-duped (you get the existing media_id back). Uploads default to public visibility so the image is emailable.  Optional alt_text and folder help organise and caption the asset. Confirm the tenant_slug with tenant_list first. To see what&#39;s already in the library, use media_list. |
| [**pricingStructureAssign()**](WriteApi.md#pricingStructureAssign) | **POST** /api/v1/tools/pricing_structure_assign | Assign a shared bundle pricing structure to one or more products. Re-syncs variations for each reassigned product. Automatically deletes orphaned inline (hidden) structures when replacing them.  SAFETY RULES enforced by this tool: - The target structure_id must be a BUNDLE (is_hidden&#x3D;false). Inline structures cannot be assigned   to products this way — that would violate the 1:1 contract. - If a product&#39;s current structure is inline (hidden) and this product is its only consumer   (product_count &#x3D;&#x3D; 1), the old inline structure is deleted automatically. - If a product&#39;s current structure is inline but product_count &gt; 1, assignment is refused for that   product with an explanation — this is a data anomaly that needs manual resolution. - Each product result includes a status: assigned | skipped (already on this structure) | refused.  Use pricing_structure_list to get valid structure IDs before calling this tool. |
| [**pricingStructureDelete()**](WriteApi.md#pricingStructureDelete) | **POST** /api/v1/tools/pricing_structure_delete | Delete one or more pricing structures by ID.  SAFETY RULES enforced by this tool: - BUNDLE structures (is_hidden&#x3D;false): always deletable unless products are still assigned.   Pass force&#x3D;true to delete even when products are assigned (use only after migrating them). - INLINE structures (is_hidden&#x3D;true): only deletable when product_count&#x3D;0 (orphaned).   Inline structures with products attached cannot be deleted — use pricing_structure_assign   to move the product to a bundle first, which auto-cleans the inline structure. |
| [**pricingStructureList()**](WriteApi.md#pricingStructureList) | **POST** /api/v1/tools/pricing_structure_list | List all pricing structures for a tenant with their kind (inline|bundle), product count, tracking type, and tier summary. Always call this before pricing_structure_upsert or pricing_structure_assign to get structure IDs and confirm which structures are bundles vs inline (1:1 product) structures.  kind&#x3D;inline  → hidden 1:1 structure tied to exactly one product (is_hidden&#x3D;true) kind&#x3D;bundle  → shared structure visible on /admin/pricing, used by 0 or more products (is_hidden&#x3D;false) |
| [**pricingStructureRestore()**](WriteApi.md#pricingStructureRestore) | **POST** /api/v1/tools/pricing_structure_restore | Surgical restore tool. Rebuilds a single product&#39;s pricing structure and variations EXACTLY to a specified state. Bypasses the standard syncVariationsForProduct routine — you control every field.  Use this AFTER inventory_audit_lookup has revealed the pre-incident state of variations whose names/prices/stock were destroyed by an erroneous bundle reassignment.  Behaviour: - Creates a NEW inline (hidden, 1:1) pricing structure with the given tracking_type, tier   definitions, and a name like \&quot;Product: &lt;product_name&gt; (Hidden)\&quot;. Old structure linkage is   replaced. The previous structure is NOT deleted by this tool. - For each tier in the spec, finds-or-creates a variation. Matching is by &#x60;restore_variation_id&#x60;   if provided, else by name. If found, the variation is updated in place (preserving its id and   its audit-log history). If not found, a new variation is created. - Stock_quantity is set EXACTLY to the value specified — this is the whole point of the tool. - Sets product.tracking_type, product.inventory_mode, product.base_unit per the new structure. - Variations on the product not referenced by any tier in the spec are DEACTIVATED (is_active&#x3D;false)   so they stop being shown but their audit history is retained. Pass &#x60;delete_unreferenced&#x3D;true&#x60; to   hard-delete them instead.  SAFETY: - Wrap each call in its own transaction. - Will refuse if the new tracking_type is incompatible with stored cost data. - Inline-only by design — bundles are not recreated by this tool. Use pricing_structure_assign   to put the product on a bundle if that&#39;s what you want. |
| [**pricingStructureUpsert()**](WriteApi.md#pricingStructureUpsert) | **POST** /api/v1/tools/pricing_structure_upsert | Create or edit a pricing structure&#39;s tiers, name, and tracking type. Operates in three modes:  BUNDLE MODE (structure_id provided, structure is not hidden):   Edit a shared bundle structure visible on /admin/pricing. Tiers are replaced and ALL products   linked to the bundle are re-synced. Returns how many products were affected as a warning.  INLINE MODE (product_slug or product_id provided, no structure_id):   Edit the hidden 1:1 pricing structure for a single product. Tiers are replaced and variations   are re-synced for that product only. Refuses if the product currently uses a bundle structure —   use pricing_structure_assign to detach from the bundle first.  CREATE BUNDLE MODE (no structure_id, no product_slug/product_id):   Create a new shared bundle structure. Does not link it to any products.  SAFETY RULES enforced by this tool: - Never accepts structure_id pointing to a hidden (inline) structure — always go via product_slug/product_id. - Tiers for weight/matrix types must have weight_grams &gt; 0. - Tiers for simple type: only the first tier is used; name and weight_grams are normalised. - Prices are accepted as dollar amounts (e.g. 12.99) and converted to cents internally. |
| [**productFeatureManage()**](WriteApi.md#productFeatureManage) | **POST** /api/v1/tools/product_feature_manage | List or set which products are featured on a tenant&#39;s storefront home page (the \&quot;Featured Products\&quot; section, HomeController — up to 8 shown, ordered by the tenant&#39;s default product sort).  ACTIONS:   list (default): return every is_featured&#x3D;true product with id, slug, name,          stock_status. Always call this first to see the current set before          changing it.   set: pass product_ids (array) and featured (bool) to set is_featured on          those products. Unlisted products are left untouched — this is an          additive/subtractive edit, not a replace-the-whole-set operation.  Products must be resolved to ids first (product_inspect or this tool&#39;s list action). Featuring an out-of-stock product is allowed but usually undesirable — check stock_status in the list output before featuring. |
| [**productImageAssign()**](WriteApi.md#productImageAssign) | **POST** /api/v1/tools/product_image_assign | Set a product&#39;s featured image and/or gallery from the tenant media library. Pass media_id (from media_list / media_upload) for featured_image, and/or gallery_media_ids to replace gallery_images. Does not upload bytes and does not create media_assets rows — ingest first with media_upload. Assets must be public (private library items have no storefront URL).  dry_run (default true) previews without writing. overwrite (default false) refuses to replace an existing featured_image unless true. clear_featured clears featured_image without setting a new one.  Distinct from product_image_strain_assign, which writes a shared platform strain CDN URL and never creates a tenant library row (GitHub #72 / #184). |
| [**productImageStrainAssign()**](WriteApi.md#productImageStrainAssign) | **POST** /api/v1/tools/product_image_strain_assign | Apply a hosted platform strain photo to a product&#39;s featured_image. Writes a URL string to the product row only — never uploads/copies bytes, never creates a tenant media_assets row (this is the shared strain library, not tenant media).  Two modes (exactly one required):   assignments: explicit [{product_id, strain_id}, ...] pairs you already     decided on (e.g. from product_image_strain_match&#39;s proposals).   auto_match: true — re-runs product_image_strain_match&#39;s own matching     internally against products missing a featured image (optionally     scoped by search/limit) and applies every exact/partial match found.  dry_run (default true) previews without writing — always call once with dry_run&#x3D;true and review the results before dry_run&#x3D;false.  overwrite (default false) — a product that already has a featured_image is skipped, never replaced, unless overwrite&#x3D;true. Idempotent: if a product&#39;s featured_image already equals the target strain&#39;s hosted URL, it is reported skipped/already_set regardless of overwrite (nothing to do, not an error).  Refuses to assign a strain whose image is not hosted on the platform (skipped/strain_image_not_hosted) — never writes a dead or third-party hotlinked URL as a product&#39;s featured image. |
| [**productManage()**](WriteApi.md#productManage) | **POST** /api/v1/tools/product_manage | Create a simple product for a tenant (v1: one price, unit tracking — the same default as a blank create-product page). Call strain_lookup first when the name looks like a strain, then pass strain_id so description, type, THC/CBD, indica/sativa, effects, flavors, rating, and the hosted strain photo fill in exactly like the vendor create page.  If strain_id is omitted, this tool name-matches the platform strain library itself: an exact name match is applied automatically; close matches are returned as strain_matches so you can offer them. Pass skip_strain_enrich&#x3D;true to skip that.  ACTIONS:   create: requires name. Optional price (dollars), sku, stock_quantity,           category_ids, description, strain_id. |
| [**productUpdateBySku()**](WriteApi.md#productUpdateBySku) | **POST** /api/v1/tools/product_update_by_sku | Update a simple product&#39;s stock quantity and/or price by SKU — the inventory-sync path for an external POS. v1 scope: SIMPLE products only (single implicit unit, no weight/variant tiers). Every other pricing type (weight, unit, matrix, matrix_unit) is rejected with a clear message; those need per-tier/per-variant targeting that a flat SKU+quantity+price payload cannot express safely. Always call product_inspect with sku first to confirm which product/type you are targeting. |
| [**purchaseOrderDraftCreate()**](WriteApi.md#purchaseOrderDraftCreate) | **POST** /api/v1/tools/purchase_order_draft_create | Create a draft purchase order for a supplier. Does not receive stock. After creating, add lines with purchase_order_line_add, then tell the vendor to review the draft in admin (never auto-receive). |
| [**purchaseOrderLineAdd()**](WriteApi.md#purchaseOrderLineAdd) | **POST** /api/v1/tools/purchase_order_line_add | Add a product line to a draft purchase order. Pass product_id (and variation_id when the product tracks stock by size). qty is the ordered quantity. unit_cost is dollars per unit; omit to use last cost. |
| [**supplierUpsert()**](WriteApi.md#supplierUpsert) | **POST** /api/v1/tools/supplier_upsert | Create or update a product supplier for purchase orders.  UPDATE MODE (supplier_id): only the fields you pass are changed. CREATE MODE (no supplier_id): name is required. If a supplier with the same name already exists for this vendor, that row is updated instead.  Always resolve the supplier_id before purchase_order_draft_create. |
| [**tenantBrandingManage()**](WriteApi.md#tenantBrandingManage) | **POST** /api/v1/tools/tenant_branding_manage | Show or update a tenant&#39;s store logo, homepage hero image, and BIMI email logo, then upsert the Cloudflare &#x60;default._bimi&#x60; TXT record when the zone is connected.  ACTIONS:   show (default): current logo_path, storefront logo URL (custom domain when          set), hero_image_path/URL, BIMI path/URL, the TXT value to publish,          and whether Cloudflare is connected.   set: pass media_id (from media_list / media_upload) to point logo_path at a          library asset. Pass hero_media_id for settings.hero_image_path, or          clear_hero&#x3D;true to remove the hero. Pass exactly one of source_base64 /          source_path / source_url for an SVG to store the BIMI logo at          tenants/{id}/bimi-logo.svg (media_upload cannot take SVG). dry_run          defaults true — the first call reports what would happen.  Logo URLs and BIMI &#x60;l&#x3D;&#x60; values always use the tenant storefront host (custom domain or {slug}.dabdash.com), never the platform APP_URL. |
| [**widgetManage()**](WriteApi.md#widgetManage) | **POST** /api/v1/tools/widget_manage | List, create, update, or delete a tenant&#39;s homepage marketing widgets (the hero slider cards linking to a product, category, featured products, or a mix &amp; match tag).  ACTIONS:   list   (default): return every widget with id, title, subtitle, link_type, target, sort_order,          is_active, and image_url. Always call this first to find a widget_id.   create: requires title. link_type + its matching id/tag is optional but recommended so the          widget&#39;s CTA actually goes somewhere (see LINK_TYPE below). Defaults to \&quot;featured\&quot;          (no target) when omitted.   update: requires widget_id. Only the fields you pass are changed.   delete: requires widget_id and confirm&#x3D;true.  LINK_TYPE — pairs with exactly one target field:   \&quot;product\&quot;    → product_id   \&quot;category\&quot;   → category_id   \&quot;mix_match\&quot;  → mix_match_tag   \&quot;featured\&quot;   → no target needed (links to the featured-products listing)  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites the headline/logo overlay onto). Omit both to leave images untouched.  Always call action&#x3D;list first to confirm widget_id before update or delete. |


## `bundleList()`

```php
bundleList($bundle_list_request): \ShadowSoftware\DabDash\Model\BundleList200Response
```

List a tenant's bundle deals (\"mix & match\" — e.g. \"buy 4 for $77\") with id, name, trigger quantity, discount type/value, active state, schedule window, and attached variation count. Bundles are the live cart engine (MixMatchService): a bundle fires when a cart holds at least `quantity` units across its attached variations. Always call this before bundle_upsert to get the bundle id and confirm the current discount configuration.  IMPORTANT: this reads the `bundles` table — the source of truth the storefront cart uses. It is NOT the legacy `mix_match_rules` tenant setting that promotion_audit / product_inspect surface; those are stale display-only data. Trust this tool for what actually applies at checkout.  discount_type:   percent      → discount_value is a percentage 0–100, applied per unit.   fixed        → discount_value is dollars off PER UNIT.   fixed_total  → discount_value is the dollar TOTAL for the whole set (\"$77 for 4\").

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$bundle_list_request = new \ShadowSoftware\DabDash\Model\BundleListRequest(); // \ShadowSoftware\DabDash\Model\BundleListRequest

try {
    $result = $apiInstance->bundleList($bundle_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->bundleList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bundle_list_request** | [**\ShadowSoftware\DabDash\Model\BundleListRequest**](../Model/BundleListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleList200Response**](../Model/BundleList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `bundleUpsert()`

```php
bundleUpsert($bundle_upsert_request): \ShadowSoftware\DabDash\Model\BundleUpsert200Response
```

Create or update a bundle deal (mix & match) on behalf of a tenant. Bundles are the live cart engine: a bundle fires when a cart holds at least `quantity` units across its attached variations.  UPDATE MODE (bundle_id provided):   Edits the bundle. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no bundle_id):   Creates a new bundle. name, quantity, discount_type, and discount_value are required.  DISCOUNT VALUE UNITS — read carefully, this is the common mistake:   discount_type = \"percent\"      → discount_value is a percentage 0–100 (e.g. 20 = 20% off each unit).   discount_type = \"fixed\"        → discount_value is DOLLARS off PER UNIT (e.g. 5 = $5 off each).   discount_type = \"fixed_total\"  → discount_value is the DOLLARS TOTAL for the whole set                                     (e.g. quantity=4, discount_value=77 → \"any 4 for $77\").   For fixed and fixed_total, pass dollars (e.g. 77 or 77.00) — the tool stores cents internally.   For percent, pass the percentage (e.g. 20), NOT a fraction.  VARIATIONS:   variation_ids + variation_mode control which product variations the bundle applies to.   mode \"replace\" (default) sets membership to exactly variation_ids; \"add\" attaches them to the   existing set; \"detach\" removes them. Variations not owned by the tenant are ignored.   Omit variation_ids entirely to leave membership untouched.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant's timezone and stored as UTC. Pass null/omit   for an always-on bundle.  Always call bundle_list first to get the bundle_id and confirm the current configuration.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$bundle_upsert_request = new \ShadowSoftware\DabDash\Model\BundleUpsertRequest(); // \ShadowSoftware\DabDash\Model\BundleUpsertRequest

try {
    $result = $apiInstance->bundleUpsert($bundle_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->bundleUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bundle_upsert_request** | [**\ShadowSoftware\DabDash\Model\BundleUpsertRequest**](../Model/BundleUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleUpsert200Response**](../Model/BundleUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignApplyTemplate()`

```php
campaignApplyTemplate($campaign_apply_template_request): \ShadowSoftware\DabDash\Model\CampaignApplyTemplate200Response
```

Apply a built-in system email template to a DRAFT campaign, replacing its html_body with the rendered, tenant-branded design. This mirrors the \"Choose template\" action in the vendor admin.  The template is rendered with the tenant's own branding (theme colour, logo, name, address, phone), then sanitized and written to the campaign's html_body. The {{unsubscribe_url}} token is preserved. The design ships with placeholder copy ([Product name], $00, \"Your headline here\", etc.) — after applying, use campaign_upsert to set the real html_body with this tenant's products, prices, and offers, or hand off to the vendor to fill in.  Only DRAFT campaigns can have a template applied. To discover valid template_id values, omit template_id (or pass an unknown one) and the tool returns the list of available templates.  Typical flow: campaign_upsert (create draft) → campaign_apply_template (lay down the design) → campaign_upsert (replace html_body with real data) → campaign_send_test (preview).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_apply_template_request = new \ShadowSoftware\DabDash\Model\CampaignApplyTemplateRequest(); // \ShadowSoftware\DabDash\Model\CampaignApplyTemplateRequest

try {
    $result = $apiInstance->campaignApplyTemplate($campaign_apply_template_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignApplyTemplate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_apply_template_request** | [**\ShadowSoftware\DabDash\Model\CampaignApplyTemplateRequest**](../Model/CampaignApplyTemplateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignApplyTemplate200Response**](../Model/CampaignApplyTemplate200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignControl()`

```php
campaignControl($campaign_control_request): \ShadowSoftware\DabDash\Model\CampaignControl200Response
```

Pause or resume a vendor email/SMS campaign.  pause: sets status=paused with paused_reason=manual. Works from sending or recovering. Pending recipients stay pending; the dispatcher skips this campaign so sibling sending campaigns can use the shared throttle.  resume: from paused → sending (clears pause fields). From recovering → sending and requeues transport-failed recipients (same as auto-resume after a healthy webhook health check).  dry_run defaults TRUE. Always confirm tenant_slug and campaign_id first.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_control_request = new \ShadowSoftware\DabDash\Model\CampaignControlRequest(); // \ShadowSoftware\DabDash\Model\CampaignControlRequest

try {
    $result = $apiInstance->campaignControl($campaign_control_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignControl: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_control_request** | [**\ShadowSoftware\DabDash\Model\CampaignControlRequest**](../Model/CampaignControlRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignControl200Response**](../Model/CampaignControl200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignRecipientsRequeue()`

```php
campaignRecipientsRequeue($campaign_recipients_requeue_request): \ShadowSoftware\DabDash\Model\CampaignRecipientsRequeue200Response
```

Unstick a vendor email/SMS campaign that auto-paused or stalled mid-send.  Resets failed recipients (and optionally stale \"sending\" rows) back to pending, clears webhook_consecutive_failures / paused_reason / last_error, and sets the campaign status to sending so the dispatcher continues. Already-sent recipients are never touched.  Pass retry_sending_log_errors=true to retry every recipient that appears in the campaign sending log with a retryable error (502/timeout/SMTP soft fail, etc.). Those jobs dispatch immediately (up to dispatch_limit) so they are not stuck behind a large pending queue. The log rows stay — they are diagnostic history.  Use when a webhook campaign froze after consecutive endpoint errors (or soft SMTP failures that were misclassified before the soft-fail fix) and pending + failed recipients remain.  dry_run defaults to TRUE — first call reports counts only. Pass dry_run=false to apply. Always confirm tenant_slug with tenant_list and campaign_id with the vendor.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_recipients_requeue_request = new \ShadowSoftware\DabDash\Model\CampaignRecipientsRequeueRequest(); // \ShadowSoftware\DabDash\Model\CampaignRecipientsRequeueRequest

try {
    $result = $apiInstance->campaignRecipientsRequeue($campaign_recipients_requeue_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignRecipientsRequeue: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_recipients_requeue_request** | [**\ShadowSoftware\DabDash\Model\CampaignRecipientsRequeueRequest**](../Model/CampaignRecipientsRequeueRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignRecipientsRequeue200Response**](../Model/CampaignRecipientsRequeue200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignSendTest()`

```php
campaignSendTest($campaign_send_test_request): \ShadowSoftware\DabDash\Model\CampaignSendTest200Response
```

Send a single test copy of a campaign so the design and copy can be reviewed before the real send.  Email campaigns go out through the tenant's own SMTP settings — exactly the path a real send uses. Pass to_email. Requires complete SMTP settings.  Text (SMS) campaigns go out through the tenant's connected Twilio account. Pass to_phone instead of to_email. Requires Twilio to be connected.  This is a preview only: it does NOT start the campaign, does NOT touch the audience, and does NOT record any send/open/click stats. Personalization tokens render with sample values; unsubscribe links point at harmless test URLs.  Works on a campaign in any status (a draft preview is the common case). Always confirm the campaign_id with the vendor first.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_send_test_request = new \ShadowSoftware\DabDash\Model\CampaignSendTestRequest(); // \ShadowSoftware\DabDash\Model\CampaignSendTestRequest

try {
    $result = $apiInstance->campaignSendTest($campaign_send_test_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignSendTest: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_send_test_request** | [**\ShadowSoftware\DabDash\Model\CampaignSendTestRequest**](../Model/CampaignSendTestRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignSendTest200Response**](../Model/CampaignSendTest200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignSetImage()`

```php
campaignSetImage($campaign_set_image_request): \ShadowSoftware\DabDash\Model\CampaignSetImage200Response
```

Drop an image from the tenant's media library into a DRAFT campaign — no link copying, no manual HTML. Give it a campaign_id and a media_id (from the tenant's own library) and it fills an image placeholder in the campaign body with that image, using the library image's own public URL and alt text. The URL is on the tenant's public disk, so it passes the campaign HTML sanitizer untouched and renders in email clients.  WHICH SLOT IT FILLS:   System templates render image placeholders labelled \"Add image\". By default this tool fills the   FIRST remaining placeholder. Pass slot_index (1-based) to target a specific placeholder when a   template has more than one. If the body has no placeholder left, the image is appended at the end.  REQUIREMENTS:   - The campaign must be a DRAFT and belong to the tenant.   - The media asset must belong to the tenant and be public (private assets have no shareable URL).  Typical flow: campaign_upsert (create) → campaign_apply_template (design with placeholders) → campaign_set_image (fill each placeholder) → campaign_upsert (real copy) → campaign_send_test.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_set_image_request = new \ShadowSoftware\DabDash\Model\CampaignSetImageRequest(); // \ShadowSoftware\DabDash\Model\CampaignSetImageRequest

try {
    $result = $apiInstance->campaignSetImage($campaign_set_image_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignSetImage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_set_image_request** | [**\ShadowSoftware\DabDash\Model\CampaignSetImageRequest**](../Model/CampaignSetImageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignSetImage200Response**](../Model/CampaignSetImage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignUpsert()`

```php
campaignUpsert($campaign_upsert_request): \ShadowSoftware\DabDash\Model\CampaignUpsert200Response
```

Create or edit a vendor email campaign on behalf of a tenant. This is the vendor → their-customers campaign universe (NOT the platform drip-to-leads system). Completed campaigns cannot be edited.  CREATE MODE (no campaign_id):   Creates a new draft. For email campaigns, `name` and `subject` are required; `html_body` is optional   on create — omit it to start blank and apply a system template afterwards with campaign_apply_template.   For text (SMS) campaigns, set channel to \"sms\" and pass `sms_body` (name required). Twilio must be   connected on the tenant.  UPDATE MODE (campaign_id provided):   Edits an existing campaign. Only the fields you pass are changed; omitted fields are left as-is.   Drafts accept all fields. Sending or paused campaigns accept content fields only — for email that is   name, subject, html_body, plain_body, fallback_first_name; for text that is name, sms_body,   and fallback_first_name. Sending campaigns keep   running and unreached recipients get the latest version. Scheduled, cancelled, and failed campaigns   must be edited from the vendor admin.  HTML BODY (email only):   Pass the full email HTML in `html_body`. It is sanitized exactly like the vendor admin editor:   a full document (<!DOCTYPE …> / <html>) keeps its table-based structure; a fragment is run   through the stricter inline allowlist. Use the literal token {{unsubscribe_url}} where the   unsubscribe link should appear — it is replaced per-recipient at send time and a List-Unsubscribe   header is added automatically. Personalization tokens {{first_name}} and {{last_name}} are also   replaced at send time. When {{first_name}} is blank, fallback_first_name is used (default \"there\"   when unset). Pass fallback_first_name as \"\" to clear an override.  SMS BODY (text only):   Pass the message in `sms_body` (max 1600 chars). Tokens {{first_name}} and {{last_name}} are   replaced at send time (same first-name fallback as email). Text campaigns only target opted-in shop customers.  AUDIENCE / MODE (email only):   mode is \"smtp\" (default) or \"webhook\". audience_includes_customers (default true) targets the   tenant's own customers. This tool does not send anything — use campaign_send_test to preview,   then the vendor sends from the admin.  Always confirm the tenant_slug with tenant_list first. To populate a draft from a built-in design, create it here, then call campaign_apply_template.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_upsert_request = new \ShadowSoftware\DabDash\Model\CampaignUpsertRequest(); // \ShadowSoftware\DabDash\Model\CampaignUpsertRequest

try {
    $result = $apiInstance->campaignUpsert($campaign_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->campaignUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_upsert_request** | [**\ShadowSoftware\DabDash\Model\CampaignUpsertRequest**](../Model/CampaignUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignUpsert200Response**](../Model/CampaignUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `catalogCollapse()`

```php
catalogCollapse($catalog_collapse_request): \ShadowSoftware\DabDash\Model\CatalogCollapse200Response
```

Merges a group of size-split products into ONE product with size options.  Turns \"Blue Dream - 3.5G\", \"Blue Dream - 7G\", \"Blue Dream - 28G\" (three separate products, each with one \"Default\" option) into a single \"Blue Dream\" product that sells by weight with 3.5g / 7g / 28g options. Stock is added together into one shared pool, and each old price becomes the price of its size.  Run catalog_flattening_audit first to see the groups and their exact base_name.  SAFETY: - dry_run defaults to TRUE. Nothing changes until you pass dry_run=false. The dry run returns   the exact sizes, prices, stock, and which products would be removed. - Past orders are never affected — they keep the product name and price the customer saw. - Refused when two products in the group claim the same size, or when any product belongs to   another store. - This removes the duplicate products. It cannot be undone from here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$catalog_collapse_request = new \ShadowSoftware\DabDash\Model\CatalogCollapseRequest(); // \ShadowSoftware\DabDash\Model\CatalogCollapseRequest

try {
    $result = $apiInstance->catalogCollapse($catalog_collapse_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->catalogCollapse: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **catalog_collapse_request** | [**\ShadowSoftware\DabDash\Model\CatalogCollapseRequest**](../Model/CatalogCollapseRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CatalogCollapse200Response**](../Model/CatalogCollapse200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `categoryManage()`

```php
categoryManage($category_manage_request): \ShadowSoftware\DabDash\Model\CategoryManage200Response
```

List, create, update, or delete a tenant's storefront categories.  ACTIONS:   list   (default): return every category with id, name, slug, parent, sort_order,          is_active, is_featured, and image_url. Always call this first to find a          category_id before update/delete, and to check for slug collisions before create.   create: requires name (slug is auto-generated from name if omitted).   update: requires category_id. Only the fields you pass are changed.   delete: requires category_id and confirm=true. Refuses if the category still has          products or children attached (detach or reassign them first).  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites branding onto). Omit both to leave images untouched.  Always call action=list first to confirm category_id / slug before update or delete.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$category_manage_request = new \ShadowSoftware\DabDash\Model\CategoryManageRequest(); // \ShadowSoftware\DabDash\Model\CategoryManageRequest

try {
    $result = $apiInstance->categoryManage($category_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->categoryManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **category_manage_request** | [**\ShadowSoftware\DabDash\Model\CategoryManageRequest**](../Model/CategoryManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CategoryManage200Response**](../Model/CategoryManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `contactCleanup()`

```php
contactCleanup($contact_cleanup_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Removes contacts that failed email validation from an imported list.  Typical use: a CSV import brought in thousands of addresses, the validator graded them, and the list is now full of dead addresses that will bounce and hurt sending reputation.  SAFETY: - dry_run defaults to TRUE. Nothing is deleted until you pass dry_run=false. The dry run   returns exactly how many contacts match and a sample of them. - Only statuses \"invalid\" and \"risky\" can be purged. \"unknown\" means the validator has not   graded that contact YET — those are never deleted, and asking for them is refused. - Scoped to one contact list when list_id is given, otherwise the whole store. - Contacts are deleted, not unsubscribed. This cannot be undone from here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$contact_cleanup_request = new \ShadowSoftware\DabDash\Model\ContactCleanupRequest(); // \ShadowSoftware\DabDash\Model\ContactCleanupRequest

try {
    $result = $apiInstance->contactCleanup($contact_cleanup_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->contactCleanup: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **contact_cleanup_request** | [**\ShadowSoftware\DabDash\Model\ContactCleanupRequest**](../Model/ContactCleanupRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ContactCleanup200Response**](../Model/ContactCleanup200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `couponUpsert()`

```php
couponUpsert($coupon_upsert_request): \ShadowSoftware\DabDash\Model\CouponUpsert200Response
```

Create or update a discount coupon on behalf of a tenant. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_upsert for those).  UPDATE MODE (coupon_id provided):   Edits the coupon. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no coupon_id):   Creates a new coupon. code, type, and value are required.  VALUE UNITS:   type = \"percentage\"     → value is a percentage 0-100 off the order subtotal.   type = \"fixed\"          → value is DOLLARS off the order subtotal (e.g. 10 = $10 off).   type = \"free_delivery\"  → value is ignored (pass 0); this type only waives the delivery fee.  min_order is a dollar minimum order subtotal required to use the coupon (pass dollars, e.g. 25 for a $25 minimum — the tool stores cents internally). Omit or pass 0 for no minimum.  freebie_id links this coupon to an existing Freebie (see freebie_list/freebie_upsert), turning it into a \"code-triggered freebie\": a customer who applies the code while below min_order has the code parked (kept attached, not rejected) and sees cart progress toward unlocking it, instead of the code being discarded. Pass null to unlink. The freebie must belong to the same tenant.  limit_match_by (\"email\"|\"phone\"|\"both\") controls how max_uses_per_customer is enforced. Using \"phone\" or \"both\" REQUIRES the tenant's \"Require phone at checkout\" setting to be on — otherwise the update is rejected, since customers without a phone on file could otherwise reuse the coupon past its per-customer limit.  SCHEDULE:   starts_at / expires_at are interpreted in the tenant's timezone and stored as UTC. Pass   null/omit for an always-on coupon.  PRODUCT/CATEGORY SCOPING:   applies_to=\"products\" or \"categories\" restricts the discount to matching cart lines only —   checkout math is fully scope-aware (a scoped coupon never discounts the whole cart). Pass   applies_to_ids as an array of product ids (when applies_to=\"products\") or category ids (when   applies_to=\"categories\") — all ids must belong to this tenant. Passing applies_to=\"products\"/   \"categories\" with an empty or omitted applies_to_ids behaves the same as \"all\" (no scope   configured yet). Switching back to applies_to=\"all\" does not automatically clear a   previously-set applies_to_ids — pass applies_to_ids=[] explicitly to clear it.  SUBSCRIPTION MOUNTING DISCOUNT LADDERS (subscribe-and-save retention):   A coupon can carry a \"mounting ladder\" so its discount climbs with each successive order a   customer's delivery subscription generates, pegging at a ceiling — e.g. order 1 = 0% off,   then +5% every order up to a 20% cap. This only affects orders generated for a subscription   linked to this coupon (DeliverySubscription.coupon_id) — it has no effect on ordinary   one-off checkout use of the code, which still uses type/value as normal.    subscription_ladder_id: link to an existing ladder (from another coupon) by id, or pass null   to unlink. Must belong to the same tenant.    ladder_start_percent / ladder_step_percent / ladder_cap_percent / ladder_start_order_index:   pass any of these to CREATE a new ladder inline (on coupon create) or EDIT the tiers of the   ladder already linked to this coupon (on update) — do not combine with subscription_ladder_id   in the same call. All four are whole-number percentages (0-100) except start_order_index,   which is the 1-based order number the climb begins at (orders before it stay at   ladder_start_percent). Omitted ladder_* fields default to 0% start / 5% step / 20% cap /   order 1 on create; on update, only the fields you pass are changed.  Always call coupon_list first to get the coupon_id and confirm the current configuration.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$coupon_upsert_request = new \ShadowSoftware\DabDash\Model\CouponUpsertRequest(); // \ShadowSoftware\DabDash\Model\CouponUpsertRequest

try {
    $result = $apiInstance->couponUpsert($coupon_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->couponUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **coupon_upsert_request** | [**\ShadowSoftware\DabDash\Model\CouponUpsertRequest**](../Model/CouponUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CouponUpsert200Response**](../Model/CouponUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `customerUpdate()`

```php
customerUpdate($customer_update_request): \ShadowSoftware\DabDash\Model\CustomerUpdate200Response
```

Update a customer's contact fields (name, email, phone) and/or suppress marketing consent (email_opt_out, sms_marketing_opt_out, sms_notifications_muted — one-way, cannot un-suppress). Verification, loyalty, and other DabDash-owned fields cannot be set here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$customer_update_request = new \ShadowSoftware\DabDash\Model\CustomerUpdateRequest(); // \ShadowSoftware\DabDash\Model\CustomerUpdateRequest

try {
    $result = $apiInstance->customerUpdate($customer_update_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->customerUpdate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **customer_update_request** | [**\ShadowSoftware\DabDash\Model\CustomerUpdateRequest**](../Model/CustomerUpdateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CustomerUpdate200Response**](../Model/CustomerUpdate200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `freebieUpsert()`

```php
freebieUpsert($freebie_upsert_request): \ShadowSoftware\DabDash\Model\FreebieUpsert200Response
```

Create or update a freebie rule (\"spend $X, get a free item\") on behalf of a tenant. Freebies are evaluated on every cart change: a rule fires once its spend_threshold is met, adding `quantity` of the configured product/variation to the cart (distinct from bundles, which fire on cart CONTENTS/quantity — use bundle_upsert for those).  UPDATE MODE (freebie_id provided):   Edits the freebie. Only the fields you pass are changed; omitted fields are left as-is,   EXCEPT category_ids, which — like bundle_upsert's variation_ids — fully replaces the category   set whenever passed (pass an empty array to clear all categories).  CREATE MODE (no freebie_id):   Creates a new freebie. name, product_id, spend_threshold, and quantity are required.  product_id and variation_id (if given) MUST belong to the same tenant — foreign ids are rejected, not silently ignored (unlike bundle_upsert's variation_ids, since a freebie needs exactly one product to give away, not a set).  spend_threshold is entered in DOLLARS (e.g. 50 for a $50 minimum spend) — the tool stores cents internally.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order.   false  → this rule cannot combine with other freebie rules.   Does NOT multiply this rule's own quantity by how many multiples of spend_threshold the cart   reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant's timezone and stored as UTC. Pass   null/omit for an always-on freebie.  Always call freebie_list first to get the freebie_id and confirm the current configuration.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$freebie_upsert_request = new \ShadowSoftware\DabDash\Model\FreebieUpsertRequest(); // \ShadowSoftware\DabDash\Model\FreebieUpsertRequest

try {
    $result = $apiInstance->freebieUpsert($freebie_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->freebieUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **freebie_upsert_request** | [**\ShadowSoftware\DabDash\Model\FreebieUpsertRequest**](../Model/FreebieUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\FreebieUpsert200Response**](../Model/FreebieUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `mediaCompose()`

```php
mediaCompose($media_compose_request): \ShadowSoftware\DabDash\Model\MediaCompose200Response
```

Build a finished campaign/storefront creative ON THE SERVER: take a base picture, drop the vendor's logo on top, add a headline and subtitle, and save the RESULT straight into the tenant's media library. Returns a media_id + public URL ready to place with campaign_set_image, category_manage, widget_manage, tenant_branding_manage, product_image_assign, or tenant_blog_upsert.  PROVIDE THE BASE (exactly one):   - base_media_id — preferred for tenant-owned photos already in the library (media_list /     media_upload). Keeps the source visible in Admin → Media.   - base_url      — public https URL for external/generated artwork. Only the composed     OUTPUT is saved as a library row; the raw URL bytes are not separately ingested. Prefer     media_upload → base_media_id when the photo should remain in the media library (#184).  PROVIDE THE LOGO (optional, at most one):   - logo_media_id / logo_url — same two options as the base (prefer logo_media_id for the     tenant logo so it stays linked in Media).   THE LOGO IS NEVER ALTERED. It is scaled to fit (aspect ratio locked) and placed as-is —   its colours, strokes and transparency are left exactly as supplied. For legibility on busy   artwork use the `scrim` option, which darkens the area BEHIND the logo and text.  LAYOUT: logo on top, headline under it, subtitle under that — the block is centred horizontally and positioned with logo_position (top / center / bottom). Sizes are given as a fraction of the base image width so they scale with any canvas. Long text is word-wrapped automatically; the response's `wrapped` flag tells you when that happened so you can shorten it.  CONSTRAINTS: sources max 5 MB, JPEG/PNG/WebP/GIF, max 8000x8000. The finished image goes through the same pipeline as media_upload — re-encoded to WebP, resized to fit, de-duplicated by content — so identical inputs return the existing media_id instead of a duplicate.  Typical flow: media_upload (or media_list) → media_compose → place with media_id.  STOREFRONT SWAG MODE (category_id or widget_id): instead of the manual layout above, pass category_id or widget_id to run the platform's own storefront branding pipeline (SwagImagesService) against that exact category or widget — the same compositor used by the dev seeder and vendor admin: tenant logo eyebrow, Bebas Neue headline (shrunk to fit long names automatically), brand-colour accent rule, and a sub-label (the tenant's live domain for categories, the widget's own subtitle for widgets). Pass base_media_id or base_url to set/replace the row's base_image_path first; omit both to recompose from whatever base_image_path is already saved. Saves directly to image_path on that category or widget — this is an internal/admin tool, not exposed to vendors.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$media_compose_request = new \ShadowSoftware\DabDash\Model\MediaComposeRequest(); // \ShadowSoftware\DabDash\Model\MediaComposeRequest

try {
    $result = $apiInstance->mediaCompose($media_compose_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->mediaCompose: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **media_compose_request** | [**\ShadowSoftware\DabDash\Model\MediaComposeRequest**](../Model/MediaComposeRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\MediaCompose200Response**](../Model/MediaCompose200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `mediaList()`

```php
mediaList($media_list_request): \ShadowSoftware\DabDash\Model\MediaList200Response
```

List the images in a tenant's media library — id, public URL, dimensions, filename, folder, and alt text. Use this to find the media_id of an image to place into a campaign with campaign_set_image, instead of guessing. Returns newest first.  Filter with `folder` (exact match) or `search` (filename substring). `visibility` defaults to \"public\" (the emailable assets); pass \"all\" or \"private\" to widen. Confirm the tenant_slug with tenant_list first.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$media_list_request = new \ShadowSoftware\DabDash\Model\MediaListRequest(); // \ShadowSoftware\DabDash\Model\MediaListRequest

try {
    $result = $apiInstance->mediaList($media_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->mediaList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **media_list_request** | [**\ShadowSoftware\DabDash\Model\MediaListRequest**](../Model/MediaListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\MediaList200Response**](../Model/MediaList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `mediaUpload()`

```php
mediaUpload($media_upload_request): \ShadowSoftware\DabDash\Model\MediaUpload200Response
```

Upload an image into a tenant's media library (the same library the vendor admin uses). The image is ingested through the platform's shared media pipeline — re-encoded to WebP, resized to fit, EXIF- oriented, content-addressed for dedup — and a media_assets row is created. It ALWAYS lands in the library, and the tool returns the new media_id and public URL.  PLACE the media_id with the matching tool (never hotlink the URL as a product/blog/hero path):   - campaign_set_image — email campaign HTML   - category_manage / widget_manage — storefront images (media_id / base_media_id)   - tenant_branding_manage — store logo (media_id) or homepage hero (hero_media_id)   - product_image_assign — product featured / gallery   - tenant_blog_upsert — vendor blog featured image   - media_compose — base_media_id / logo_media_id for composed creatives  PROVIDE THE IMAGE ONE OF THREE WAYS (exactly one):   - source_url    — fetch the image from a public http(s) URL (preferred for remote agents).   - source_path   — read a local file path on the server/agent host (often broken on prod FS).   - source_base64 — raw base64 of the image bytes (no data: prefix needed; a data: prefix is stripped).  CONSTRAINTS: max 5 MB; JPEG, PNG, WebP, or GIF. Identical bytes already in the library are de-duped (you get the existing media_id back). Uploads default to public visibility so the image is emailable.  Optional alt_text and folder help organise and caption the asset. Confirm the tenant_slug with tenant_list first. To see what's already in the library, use media_list.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$media_upload_request = new \ShadowSoftware\DabDash\Model\MediaUploadRequest(); // \ShadowSoftware\DabDash\Model\MediaUploadRequest

try {
    $result = $apiInstance->mediaUpload($media_upload_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->mediaUpload: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **media_upload_request** | [**\ShadowSoftware\DabDash\Model\MediaUploadRequest**](../Model/MediaUploadRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\MediaUpload200Response**](../Model/MediaUpload200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureAssign()`

```php
pricingStructureAssign($pricing_structure_assign_request): \ShadowSoftware\DabDash\Model\PricingStructureAssign200Response
```

Assign a shared bundle pricing structure to one or more products. Re-syncs variations for each reassigned product. Automatically deletes orphaned inline (hidden) structures when replacing them.  SAFETY RULES enforced by this tool: - The target structure_id must be a BUNDLE (is_hidden=false). Inline structures cannot be assigned   to products this way — that would violate the 1:1 contract. - If a product's current structure is inline (hidden) and this product is its only consumer   (product_count == 1), the old inline structure is deleted automatically. - If a product's current structure is inline but product_count > 1, assignment is refused for that   product with an explanation — this is a data anomaly that needs manual resolution. - Each product result includes a status: assigned | skipped (already on this structure) | refused.  Use pricing_structure_list to get valid structure IDs before calling this tool.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_assign_request = new \ShadowSoftware\DabDash\Model\PricingStructureAssignRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureAssignRequest

try {
    $result = $apiInstance->pricingStructureAssign($pricing_structure_assign_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->pricingStructureAssign: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_assign_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureAssignRequest**](../Model/PricingStructureAssignRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureAssign200Response**](../Model/PricingStructureAssign200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureDelete()`

```php
pricingStructureDelete($pricing_structure_delete_request): \ShadowSoftware\DabDash\Model\PricingStructureDelete200Response
```

Delete one or more pricing structures by ID.  SAFETY RULES enforced by this tool: - BUNDLE structures (is_hidden=false): always deletable unless products are still assigned.   Pass force=true to delete even when products are assigned (use only after migrating them). - INLINE structures (is_hidden=true): only deletable when product_count=0 (orphaned).   Inline structures with products attached cannot be deleted — use pricing_structure_assign   to move the product to a bundle first, which auto-cleans the inline structure.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_delete_request = new \ShadowSoftware\DabDash\Model\PricingStructureDeleteRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureDeleteRequest

try {
    $result = $apiInstance->pricingStructureDelete($pricing_structure_delete_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->pricingStructureDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_delete_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureDeleteRequest**](../Model/PricingStructureDeleteRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureDelete200Response**](../Model/PricingStructureDelete200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureList()`

```php
pricingStructureList($pricing_structure_list_request): \ShadowSoftware\DabDash\Model\PricingStructureList200Response
```

List all pricing structures for a tenant with their kind (inline|bundle), product count, tracking type, and tier summary. Always call this before pricing_structure_upsert or pricing_structure_assign to get structure IDs and confirm which structures are bundles vs inline (1:1 product) structures.  kind=inline  → hidden 1:1 structure tied to exactly one product (is_hidden=true) kind=bundle  → shared structure visible on /admin/pricing, used by 0 or more products (is_hidden=false)

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_list_request = new \ShadowSoftware\DabDash\Model\PricingStructureListRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureListRequest

try {
    $result = $apiInstance->pricingStructureList($pricing_structure_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->pricingStructureList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_list_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureListRequest**](../Model/PricingStructureListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureList200Response**](../Model/PricingStructureList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureRestore()`

```php
pricingStructureRestore($pricing_structure_restore_request): \ShadowSoftware\DabDash\Model\PricingStructureRestore200Response
```

Surgical restore tool. Rebuilds a single product's pricing structure and variations EXACTLY to a specified state. Bypasses the standard syncVariationsForProduct routine — you control every field.  Use this AFTER inventory_audit_lookup has revealed the pre-incident state of variations whose names/prices/stock were destroyed by an erroneous bundle reassignment.  Behaviour: - Creates a NEW inline (hidden, 1:1) pricing structure with the given tracking_type, tier   definitions, and a name like \"Product: <product_name> (Hidden)\". Old structure linkage is   replaced. The previous structure is NOT deleted by this tool. - For each tier in the spec, finds-or-creates a variation. Matching is by `restore_variation_id`   if provided, else by name. If found, the variation is updated in place (preserving its id and   its audit-log history). If not found, a new variation is created. - Stock_quantity is set EXACTLY to the value specified — this is the whole point of the tool. - Sets product.tracking_type, product.inventory_mode, product.base_unit per the new structure. - Variations on the product not referenced by any tier in the spec are DEACTIVATED (is_active=false)   so they stop being shown but their audit history is retained. Pass `delete_unreferenced=true` to   hard-delete them instead.  SAFETY: - Wrap each call in its own transaction. - Will refuse if the new tracking_type is incompatible with stored cost data. - Inline-only by design — bundles are not recreated by this tool. Use pricing_structure_assign   to put the product on a bundle if that's what you want.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_restore_request = new \ShadowSoftware\DabDash\Model\PricingStructureRestoreRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureRestoreRequest

try {
    $result = $apiInstance->pricingStructureRestore($pricing_structure_restore_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->pricingStructureRestore: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_restore_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureRestoreRequest**](../Model/PricingStructureRestoreRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureRestore200Response**](../Model/PricingStructureRestore200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureUpsert()`

```php
pricingStructureUpsert($pricing_structure_upsert_request): \ShadowSoftware\DabDash\Model\PricingStructureUpsert200Response
```

Create or edit a pricing structure's tiers, name, and tracking type. Operates in three modes:  BUNDLE MODE (structure_id provided, structure is not hidden):   Edit a shared bundle structure visible on /admin/pricing. Tiers are replaced and ALL products   linked to the bundle are re-synced. Returns how many products were affected as a warning.  INLINE MODE (product_slug or product_id provided, no structure_id):   Edit the hidden 1:1 pricing structure for a single product. Tiers are replaced and variations   are re-synced for that product only. Refuses if the product currently uses a bundle structure —   use pricing_structure_assign to detach from the bundle first.  CREATE BUNDLE MODE (no structure_id, no product_slug/product_id):   Create a new shared bundle structure. Does not link it to any products.  SAFETY RULES enforced by this tool: - Never accepts structure_id pointing to a hidden (inline) structure — always go via product_slug/product_id. - Tiers for weight/matrix types must have weight_grams > 0. - Tiers for simple type: only the first tier is used; name and weight_grams are normalised. - Prices are accepted as dollar amounts (e.g. 12.99) and converted to cents internally.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_upsert_request = new \ShadowSoftware\DabDash\Model\PricingStructureUpsertRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureUpsertRequest

try {
    $result = $apiInstance->pricingStructureUpsert($pricing_structure_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->pricingStructureUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_upsert_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureUpsertRequest**](../Model/PricingStructureUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureUpsert200Response**](../Model/PricingStructureUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productFeatureManage()`

```php
productFeatureManage($product_feature_manage_request): \ShadowSoftware\DabDash\Model\ProductFeatureManage200Response
```

List or set which products are featured on a tenant's storefront home page (the \"Featured Products\" section, HomeController — up to 8 shown, ordered by the tenant's default product sort).  ACTIONS:   list (default): return every is_featured=true product with id, slug, name,          stock_status. Always call this first to see the current set before          changing it.   set: pass product_ids (array) and featured (bool) to set is_featured on          those products. Unlisted products are left untouched — this is an          additive/subtractive edit, not a replace-the-whole-set operation.  Products must be resolved to ids first (product_inspect or this tool's list action). Featuring an out-of-stock product is allowed but usually undesirable — check stock_status in the list output before featuring.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_feature_manage_request = new \ShadowSoftware\DabDash\Model\ProductFeatureManageRequest(); // \ShadowSoftware\DabDash\Model\ProductFeatureManageRequest

try {
    $result = $apiInstance->productFeatureManage($product_feature_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productFeatureManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_feature_manage_request** | [**\ShadowSoftware\DabDash\Model\ProductFeatureManageRequest**](../Model/ProductFeatureManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductFeatureManage200Response**](../Model/ProductFeatureManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productImageAssign()`

```php
productImageAssign($product_image_assign_request): \ShadowSoftware\DabDash\Model\ProductImageAssign200Response
```

Set a product's featured image and/or gallery from the tenant media library. Pass media_id (from media_list / media_upload) for featured_image, and/or gallery_media_ids to replace gallery_images. Does not upload bytes and does not create media_assets rows — ingest first with media_upload. Assets must be public (private library items have no storefront URL).  dry_run (default true) previews without writing. overwrite (default false) refuses to replace an existing featured_image unless true. clear_featured clears featured_image without setting a new one.  Distinct from product_image_strain_assign, which writes a shared platform strain CDN URL and never creates a tenant library row (GitHub #72 / #184).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_image_assign_request = new \ShadowSoftware\DabDash\Model\ProductImageAssignRequest(); // \ShadowSoftware\DabDash\Model\ProductImageAssignRequest

try {
    $result = $apiInstance->productImageAssign($product_image_assign_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productImageAssign: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_image_assign_request** | [**\ShadowSoftware\DabDash\Model\ProductImageAssignRequest**](../Model/ProductImageAssignRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductImageAssign200Response**](../Model/ProductImageAssign200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productImageStrainAssign()`

```php
productImageStrainAssign($product_image_strain_assign_request): \ShadowSoftware\DabDash\Model\ProductImageStrainAssign200Response
```

Apply a hosted platform strain photo to a product's featured_image. Writes a URL string to the product row only — never uploads/copies bytes, never creates a tenant media_assets row (this is the shared strain library, not tenant media).  Two modes (exactly one required):   assignments: explicit [{product_id, strain_id}, ...] pairs you already     decided on (e.g. from product_image_strain_match's proposals).   auto_match: true — re-runs product_image_strain_match's own matching     internally against products missing a featured image (optionally     scoped by search/limit) and applies every exact/partial match found.  dry_run (default true) previews without writing — always call once with dry_run=true and review the results before dry_run=false.  overwrite (default false) — a product that already has a featured_image is skipped, never replaced, unless overwrite=true. Idempotent: if a product's featured_image already equals the target strain's hosted URL, it is reported skipped/already_set regardless of overwrite (nothing to do, not an error).  Refuses to assign a strain whose image is not hosted on the platform (skipped/strain_image_not_hosted) — never writes a dead or third-party hotlinked URL as a product's featured image.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_image_strain_assign_request = new \ShadowSoftware\DabDash\Model\ProductImageStrainAssignRequest(); // \ShadowSoftware\DabDash\Model\ProductImageStrainAssignRequest

try {
    $result = $apiInstance->productImageStrainAssign($product_image_strain_assign_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productImageStrainAssign: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_image_strain_assign_request** | [**\ShadowSoftware\DabDash\Model\ProductImageStrainAssignRequest**](../Model/ProductImageStrainAssignRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductImageStrainAssign200Response**](../Model/ProductImageStrainAssign200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productManage()`

```php
productManage($product_manage_request): \ShadowSoftware\DabDash\Model\ProductManage200Response
```

Create a simple product for a tenant (v1: one price, unit tracking — the same default as a blank create-product page). Call strain_lookup first when the name looks like a strain, then pass strain_id so description, type, THC/CBD, indica/sativa, effects, flavors, rating, and the hosted strain photo fill in exactly like the vendor create page.  If strain_id is omitted, this tool name-matches the platform strain library itself: an exact name match is applied automatically; close matches are returned as strain_matches so you can offer them. Pass skip_strain_enrich=true to skip that.  ACTIONS:   create: requires name. Optional price (dollars), sku, stock_quantity,           category_ids, description, strain_id.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_manage_request = new \ShadowSoftware\DabDash\Model\ProductManageRequest(); // \ShadowSoftware\DabDash\Model\ProductManageRequest

try {
    $result = $apiInstance->productManage($product_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_manage_request** | [**\ShadowSoftware\DabDash\Model\ProductManageRequest**](../Model/ProductManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductManage200Response**](../Model/ProductManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productUpdateBySku()`

```php
productUpdateBySku($product_update_by_sku_request): \ShadowSoftware\DabDash\Model\ProductUpdateBySku200Response
```

Update a simple product's stock quantity and/or price by SKU — the inventory-sync path for an external POS. v1 scope: SIMPLE products only (single implicit unit, no weight/variant tiers). Every other pricing type (weight, unit, matrix, matrix_unit) is rejected with a clear message; those need per-tier/per-variant targeting that a flat SKU+quantity+price payload cannot express safely. Always call product_inspect with sku first to confirm which product/type you are targeting.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_update_by_sku_request = new \ShadowSoftware\DabDash\Model\ProductUpdateBySkuRequest(); // \ShadowSoftware\DabDash\Model\ProductUpdateBySkuRequest

try {
    $result = $apiInstance->productUpdateBySku($product_update_by_sku_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->productUpdateBySku: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_update_by_sku_request** | [**\ShadowSoftware\DabDash\Model\ProductUpdateBySkuRequest**](../Model/ProductUpdateBySkuRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductUpdateBySku200Response**](../Model/ProductUpdateBySku200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `purchaseOrderDraftCreate()`

```php
purchaseOrderDraftCreate($purchase_order_draft_create_request): \ShadowSoftware\DabDash\Model\PurchaseOrderDraftCreate200Response
```

Create a draft purchase order for a supplier. Does not receive stock. After creating, add lines with purchase_order_line_add, then tell the vendor to review the draft in admin (never auto-receive).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$purchase_order_draft_create_request = new \ShadowSoftware\DabDash\Model\PurchaseOrderDraftCreateRequest(); // \ShadowSoftware\DabDash\Model\PurchaseOrderDraftCreateRequest

try {
    $result = $apiInstance->purchaseOrderDraftCreate($purchase_order_draft_create_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->purchaseOrderDraftCreate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **purchase_order_draft_create_request** | [**\ShadowSoftware\DabDash\Model\PurchaseOrderDraftCreateRequest**](../Model/PurchaseOrderDraftCreateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PurchaseOrderDraftCreate200Response**](../Model/PurchaseOrderDraftCreate200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `purchaseOrderLineAdd()`

```php
purchaseOrderLineAdd($purchase_order_line_add_request): \ShadowSoftware\DabDash\Model\PurchaseOrderLineAdd200Response
```

Add a product line to a draft purchase order. Pass product_id (and variation_id when the product tracks stock by size). qty is the ordered quantity. unit_cost is dollars per unit; omit to use last cost.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$purchase_order_line_add_request = new \ShadowSoftware\DabDash\Model\PurchaseOrderLineAddRequest(); // \ShadowSoftware\DabDash\Model\PurchaseOrderLineAddRequest

try {
    $result = $apiInstance->purchaseOrderLineAdd($purchase_order_line_add_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->purchaseOrderLineAdd: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **purchase_order_line_add_request** | [**\ShadowSoftware\DabDash\Model\PurchaseOrderLineAddRequest**](../Model/PurchaseOrderLineAddRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PurchaseOrderLineAdd200Response**](../Model/PurchaseOrderLineAdd200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `supplierUpsert()`

```php
supplierUpsert($supplier_upsert_request): \ShadowSoftware\DabDash\Model\SupplierUpsert200Response
```

Create or update a product supplier for purchase orders.  UPDATE MODE (supplier_id): only the fields you pass are changed. CREATE MODE (no supplier_id): name is required. If a supplier with the same name already exists for this vendor, that row is updated instead.  Always resolve the supplier_id before purchase_order_draft_create.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$supplier_upsert_request = new \ShadowSoftware\DabDash\Model\SupplierUpsertRequest(); // \ShadowSoftware\DabDash\Model\SupplierUpsertRequest

try {
    $result = $apiInstance->supplierUpsert($supplier_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->supplierUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **supplier_upsert_request** | [**\ShadowSoftware\DabDash\Model\SupplierUpsertRequest**](../Model/SupplierUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\SupplierUpsert200Response**](../Model/SupplierUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantBrandingManage()`

```php
tenantBrandingManage($tenant_branding_manage_request): \ShadowSoftware\DabDash\Model\TenantBrandingManage200Response
```

Show or update a tenant's store logo, homepage hero image, and BIMI email logo, then upsert the Cloudflare `default._bimi` TXT record when the zone is connected.  ACTIONS:   show (default): current logo_path, storefront logo URL (custom domain when          set), hero_image_path/URL, BIMI path/URL, the TXT value to publish,          and whether Cloudflare is connected.   set: pass media_id (from media_list / media_upload) to point logo_path at a          library asset. Pass hero_media_id for settings.hero_image_path, or          clear_hero=true to remove the hero. Pass exactly one of source_base64 /          source_path / source_url for an SVG to store the BIMI logo at          tenants/{id}/bimi-logo.svg (media_upload cannot take SVG). dry_run          defaults true — the first call reports what would happen.  Logo URLs and BIMI `l=` values always use the tenant storefront host (custom domain or {slug}.dabdash.com), never the platform APP_URL.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$tenant_branding_manage_request = new \ShadowSoftware\DabDash\Model\TenantBrandingManageRequest(); // \ShadowSoftware\DabDash\Model\TenantBrandingManageRequest

try {
    $result = $apiInstance->tenantBrandingManage($tenant_branding_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->tenantBrandingManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_branding_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantBrandingManageRequest**](../Model/TenantBrandingManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantBrandingManage200Response**](../Model/TenantBrandingManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `widgetManage()`

```php
widgetManage($widget_manage_request): \ShadowSoftware\DabDash\Model\WidgetManage200Response
```

List, create, update, or delete a tenant's homepage marketing widgets (the hero slider cards linking to a product, category, featured products, or a mix & match tag).  ACTIONS:   list   (default): return every widget with id, title, subtitle, link_type, target, sort_order,          is_active, and image_url. Always call this first to find a widget_id.   create: requires title. link_type + its matching id/tag is optional but recommended so the          widget's CTA actually goes somewhere (see LINK_TYPE below). Defaults to \"featured\"          (no target) when omitted.   update: requires widget_id. Only the fields you pass are changed.   delete: requires widget_id and confirm=true.  LINK_TYPE — pairs with exactly one target field:   \"product\"    → product_id   \"category\"   → category_id   \"mix_match\"  → mix_match_tag   \"featured\"   → no target needed (links to the featured-products listing)  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites the headline/logo overlay onto). Omit both to leave images untouched.  Always call action=list first to confirm widget_id before update or delete.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\WriteApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$widget_manage_request = new \ShadowSoftware\DabDash\Model\WidgetManageRequest(); // \ShadowSoftware\DabDash\Model\WidgetManageRequest

try {
    $result = $apiInstance->widgetManage($widget_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling WriteApi->widgetManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **widget_manage_request** | [**\ShadowSoftware\DabDash\Model\WidgetManageRequest**](../Model/WidgetManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\WidgetManage200Response**](../Model/WidgetManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
